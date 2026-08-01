/* =====================================================================
   ویرایشگر ترکیبی صفحه‌ساز — نگارش کامل (Phase 1 ادغام)
   
   ویژگی‌ها:
   - کلیک تکی: انتخاب بلاک (نوار ابزار بالا/پایین/ویرایش/حذف)
   - کلیک دوبل: ورود به حالت ویرایش متنی (CKEditor 5 Inline)
   - منوی زمینه‌ای برای تصویر و دکمه (آیکون در نوار ابزار → prompt)
   - ارتباط با والد از طریق postMessage
   - ادیتور مناسب: CKEditor 5 Inline (ویرایش متن) + TinyMCE (در فرم سایدبار)
       
   اسکریپت درون iframe پیش‌نمایش لود می‌شود.
   ===================================================================== */

(function () {
    'use strict';

    /* ---------- State ---------- */
    var contentFieldMap = {};
    var selected = -1;
    var selectedBlockEl = null;
    var toolbar = null;
    var dragEl = null;
    var editOverlay = null;
    var ckEditors = {};
    var ckReady = false;
    var dblClickTimer = null;
    var editModeEl = null;

    /* ---------- Utilities ---------- */
    function post(msg) {
        msg._ns = 'builderInline';
        try { window.parent.postMessage(msg, window.location.origin); } catch (e) {}
    }

    function findBlockIndex(el) {
        var block = el.closest('[data-block-index]');
        if (!block) return -1;
        return parseInt(block.getAttribute('data-block-index'), 10);
    }

    /* ---------- Block Selection ---------- */
    function selectBlock(index, el) {
        selected = index;
        selectedBlockEl = el;
        if (editModeEl) exitEditMode();
        document.querySelectorAll('.builder-live-block').forEach(function (b) {
            b.classList.remove('builder-selected');
        });
        if (el) el.classList.add('builder-selected');
        showToolbar(index, el);
        post({ type: 'builderSelect', index: index });
    }

    function deselectAll() {
        selected = -1;
        selectedBlockEl = null;
        document.querySelectorAll('.builder-live-block').forEach(function (b) {
            b.classList.remove('builder-selected');
        });
        if (toolbar) toolbar.style.display = 'none';
    }

    /* ---------- Toolbar ---------- */
    function showToolbar(index, el) {
        if (!toolbar) {
            toolbar = document.createElement('div');
            toolbar.className = 'builder-inline-toolbar';
            toolbar.innerHTML =
                '<button data-act="edit" title="ویرایش بلاک"><i class="fa-solid fa-pen"></i></button>' +
                '<button data-act="img" title="ویرایش تصویر"><i class="fa-solid fa-image"></i></button>' +
                '<button data-act="link" title="ویرایش لینک"><i class="fa-solid fa-link"></i></button>' +
                '<button data-act="up" title="بالا"><i class="fa-solid fa-arrow-up"></i></button>' +
                '<button data-act="down" title="پایین"><i class="fa-solid fa-arrow-down"></i></button>' +
                '<button data-act="del" title="حذف" class="danger"><i class="fa-solid fa-trash"></i></button>';
            document.body.appendChild(toolbar);
            toolbar.addEventListener('click', function (e) {
                var btn = e.target.closest('button');
                if (!btn) return;
                e.stopPropagation();
                var act = btn.dataset.act;
                if (act === 'edit') post({ type: 'builderSelect', index: selected });
                else if (act === 'img') openImageEditor();
                else if (act === 'link') openLinkEditor();
                else if (act === 'up') post({ type: 'builderMove', index: selected, dir: 'up' });
                else if (act === 'down') post({ type: 'builderMove', index: selected, dir: 'down' });
                else if (act === 'del') post({ type: 'builderDelete', index: selected });
            });
        }
        toolbar.style.display = 'flex';
        var rect = el.getBoundingClientRect();
        var top = Math.max(4, rect.top - 38);
        toolbar.style.top = top + 'px';
        toolbar.style.left = Math.max(4, rect.right - 150) + 'px';
    }

    /* ---------- CKEditor Inline Editing ---------- */
    function initCKEditor() {
        if (ckReady) return;
        if (typeof ClassicEditor === 'undefined' && typeof InlineEditor === 'undefined') {
            setTimeout(initCKEditor, 300);
            return;
        }
        ckReady = true;
    }

    function startCKEditorInline(el, blockIndex) {
        if (!ckReady) {
            initCKEditor();
        }
        var field = el.getAttribute('data-field') || 'text';
        if (el.isContentEditable) {
            el.contentEditable = 'false';
        }
        if (ckEditors[blockIndex]) {
            try { ckEditors[blockIndex].destroy(); } catch (_) {}
            delete ckEditors[blockIndex];
        }
        var editorInstance = null;
        var CKClass = (typeof InlineEditor !== 'undefined') ? InlineEditor : null;
        if (!CKClass && typeof ClassicEditor !== 'undefined') {
            CKClass = ClassicEditor;
        }
        if (CKClass && el) {
            CKClass.create(el, {
                placeholder: '',
                toolbar: [ 'bold', 'italic', 'strikethrough', 'link', '|', 'bulletedList', 'numberedList', '|', 'heading', 'blockquote', 'code', '|', 'undo', 'redo' ],
                removePlugins: ['EasyImage', 'Image', 'ImageUpload', 'MediaEmbed', 'Table'],
                height: 200
            }).then(function (editor) {
                editorInstance = editor;
                ckEditors[blockIndex] = editor;
                editor.model.document.on('change:data', function () {
                    var data = editor.getData();
                    post({ type: 'builderContent', index: blockIndex, key: field, value: data });
                    scheduleAutoSave();
                });
            }).catch(function (err) {
                // console.error( err );
            });
        }
        editModeEl = { el: el, blockIndex: blockIndex, field: field, editor: editorInstance };
    }

    function exitEditMode() {
        if (!editModeEl) return;
        if (editModeEl.editor) {
            try { editModeEl.editor.destroy(); } catch (_) {}
        }
        if (editModeEl.el) {
            editModeEl.el.contentEditable = 'false';
        }
        editModeEl = null;
    }

    /* ---------- Context Editors (Image / Link) ---------- */
    function openImageEditor() {
        if (selected < 0 || !selectedBlockEl) return;
        var imgs = selectedBlockEl.querySelectorAll('.builder-editable-image');
        if (!imgs.length) {
            var anyImg = selectedBlockEl.querySelector('img');
            if (!anyImg) return;
            imgs = [anyImg];
        }
        var img = imgs[0];
        var currentSrc = img.getAttribute('src') || '';
        var currentAlt = img.getAttribute('alt') || '';
        var newSrc = prompt('آدرس تصویر:', currentSrc);
        if (newSrc === null) return;
        if (newSrc !== currentSrc) {
            img.setAttribute('src', newSrc);
            post({ type: 'builderImageEdit', index: selected, src: newSrc, alt: currentAlt });
        }
    }

    function openLinkEditor() {
        if (selected < 0 || !selectedBlockEl) return;
        var btn = selectedBlockEl.querySelector('a.dakmeh, a.btn, a.builder-editable-link');
        if (!btn) return;
        var currentHref = btn.getAttribute('href') || '';
        var currentText = btn.innerText.trim();
        var newHref = prompt('لینک دکمه:', currentHref);
        if (newHref === null) return;
        if (newHref !== currentHref) {
            btn.setAttribute('href', newHref);
            post({ type: 'builderButtonEdit', index: selected, text: currentText, href: newHref });
        }
    }

    /* ---------- Reorder ---------- */
    function postOrder() {
        var order = [];
        document.querySelectorAll('[data-block-index]').forEach(function (el) {
            order.push(parseInt(el.getAttribute('data-block-index'), 10));
        });
        post({ type: 'builderReorder', order: order });
    }

    /* ---------- Drag & Drop ---------- */
    var dragStartEl = null;
    function onDragStart(e) {
        dragStartEl = this.parentNode;
        try {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.getAttribute('data-block-index'));
        } catch (_) {}
        if (dragStartEl) dragStartEl.style.opacity = '0.4';
    }
    function onDragOver(e) {
        if (dragStartEl && dragStartEl !== this) {
            e.preventDefault();
            this.classList.add('builder-drop-target');
        }
    }
    function onDragLeave() {
        this.classList.remove('builder-drop-target');
    }
    function onDrop(e) {
        if (!dragStartEl) return;
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('builder-drop-target');
        var parent = this.parentNode;
        if (parent && dragStartEl !== this) {
            var rect = this.getBoundingClientRect();
            var before = (e.clientY < rect.top + rect.height / 2);
            if (before) parent.insertBefore(dragStartEl, this);
            else parent.insertBefore(dragStartEl, this.nextSibling);
        }
    }
    function onDragEnd() {
        if (dragStartEl) dragStartEl.style.opacity = '';
        document.querySelectorAll('.builder-drop-target').forEach(function (b) {
            b.classList.remove('builder-drop-target');
        });
        dragStartEl = null;
        postOrder();
    }

    /* ---------- Init ---------- */
    function init() {
        initCKEditor();

        var root = document.querySelector('.builder-edit-root');
        var blocks = root
            ? root.querySelectorAll('[data-block-index]')
            : document.querySelectorAll('[data-block-index]');

        blocks.forEach(function (el) {
            var index = parseInt(el.getAttribute('data-block-index'), 10);
            if (isNaN(index)) return;

            el.classList.add('builder-live-block');

            /* Single-click: انتخاب بلاک */
            el.addEventListener('click', function (e) {
                /* اگر در حالت ویرایش متنی هستیم، کلیک بیرون بستن ادیتور */
                if (editModeEl && e.target !== editModeEl.el) {
                    exitEditMode();
                }
                e.stopPropagation();
                selectBlock(index, el);
            });

            /* Double-click: ورود به حالت ویرایش متن */
            el.addEventListener('dblclick', function (e) {
                e.stopPropagation();
                e.preventDefault();
                /* فقط اگر روی عنصر متنی باشد */
                var txt = e.target.closest('.builder-editable');
                if (!txt) {
                    /* اولین عنصر متنی داخل بلاک */
                    txt = el.querySelector('.builder-editable');
                }
                if (txt && txt.classList.contains('builder-text')) {
                    exitEditMode();
                    startCKEditorInline(txt, index);
                }
            });

            /* Prevent drag handle clicks from selecting */
            el.addEventListener('mousedown', function (e) {
                if (e.target.closest('.builder-drag-handle')) {
                    e.stopPropagation();
                }
            });

            /* Drag handle setup */
            var handle = document.createElement('div');
            handle.className = 'builder-drag-handle';
            handle.title = 'جابجایی';
            handle.innerHTML = '<i class="fa-solid fa-grip-vertical"></i>';
            handle.setAttribute('draggable', 'true');
            handle.addEventListener('dragstart', onDragStart);
            handle.addEventListener('dragend', onDragEnd);
            el.insertBefore(handle, el.firstChild);

            /* Mark all builder-editable elements inside */
            var editables = el.querySelectorAll('.builder-editable');
            editables.forEach(function (ed) {
                ed.style.cursor = 'text';
            });

            /* If block has exactly one text element, make it the default editable field */
            var firstText = el.querySelector('.builder-text');
            if (firstText) {
                var field = firstText.getAttribute('data-field') || 'text';
                /* Map the first text element's field to this block index */
                if (!contentFieldMap[index]) contentFieldMap[index] = field;
            }
        });

        /* Click outside: deselect */
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.builder-live-block') &&
                !e.target.closest('.builder-inline-toolbar') &&
                !e.target.closest('.builder-edit-modal')) {
                deselectAll();
            }
        });

        post({ type: 'builderReady' });
    }

    /* ---------- Auto-save ---------- */
    var autoSaveTimeout = null;
    function scheduleAutoSave() {
        if (autoSaveTimeout) clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function () {
            post({ type: 'builderAutoSave' });
        }, 1500);
    }

    /* ---------- Message listener (for parent → iframe commands) ---------- */
    window.addEventListener('message', function (e) {
        if (e.origin !== window.location.origin) return;
        var d = e.data || {};
        if (d._ns !== 'builderInline') return;
        if (d.type === 'builderSetContentFields') {
            if (d.fields) {
                for (var k in d.fields) contentFieldMap[k] = d.fields[k];
            }
        }
        else if (d.type === 'builderEditBlock') {
            if (selectedBlockEl) {
                var txt = selectedBlockEl.querySelector('.builder-text');
                if (txt) {
                    exitEditMode();
                    startCKEditorInline(txt, selected);
                }
            }
        }
        else if (d.type === 'builderExit') {
            exitEditMode();
            deselectAll();
        }
        else if (d.type === 'builderFocusBlock') {
            /* Highlight a specific block in the preview (called from parent sidebar) */
            var target = document.querySelector('[data-block-index="' + d.index + '"]');
            if (target) {
                document.querySelectorAll('.builder-live-block').forEach(function (b) {
                    b.classList.remove('builder-selected');
                });
                target.classList.add('builder-selected');
                selected = d.index;
                selectedBlockEl = target;
            }
        }
    });

    /* ---------- Boot ---------- */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();