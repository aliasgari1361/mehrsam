/* ویرایش درجا در iframe پیش‌نمایش صفحه‌ساز
 * ارتباط با پنجرهٔ والد از طریق postMessage.
 */
(function () {
    'use strict';
    var contentMap = {};   // index -> کلید فیلد متنی
    var selected = -1;
    var toolbar = null;
    var dragEl = null;

    function post(msg) {
        msg._ns = 'builderInline';
        try { window.parent.postMessage(msg, window.location.origin); } catch (e) {}
    }

    function makeEditable(el, index) {
        if (!el) return;
        el.setAttribute('contenteditable', 'true');
        el.style.outline = '1px dashed #FF6F00';
        el.style.cursor = 'text';
        el.addEventListener('blur', function () {
            var key = contentMap[index] || 'text';
            post({ type: 'builderContent', index: index, key: key, value: el.innerText });
        });
    }

    function selectBlock(index, el) {
        selected = index;
        document.querySelectorAll('.builder-live-block').forEach(function (b) {
            b.classList.remove('builder-selected');
        });
        if (el) el.classList.add('builder-selected');
        showToolbar(index, el);
    }

    function showToolbar(index, el) {
        if (!toolbar) {
            toolbar = document.createElement('div');
            toolbar.className = 'builder-inline-toolbar';
            toolbar.innerHTML =
                '<button data-act="edit" title="ویرایش"><i class="fa-solid fa-pen"></i></button>' +
                '<button data-act="up" title="بالا"><i class="fa-solid fa-arrow-up"></i></button>' +
                '<button data-act="down" title="پایین"><i class="fa-solid fa-arrow-down"></i></button>' +
                '<button data-act="del" title="حذف" class="danger"><i class="fa-solid fa-trash"></i></button>';
            document.body.appendChild(toolbar);
            toolbar.addEventListener('click', function (e) {
                var btn = e.target.closest('button');
                if (!btn) return;
                var act = btn.dataset.act;
                if (act === 'edit') post({ type: 'builderSelect', index: selected });
                else if (act === 'up') post({ type: 'builderMove', index: selected, dir: 'up' });
                else if (act === 'down') post({ type: 'builderMove', index: selected, dir: 'down' });
                else if (act === 'del') post({ type: 'builderDelete', index: selected });
                if (toolbar) toolbar.style.display = 'none';
            });
        }
        toolbar.style.display = 'flex';
        var rect = el.getBoundingClientRect();
        var top = Math.max(4, rect.top - 38);
        toolbar.style.top = top + 'px';
        toolbar.style.left = Math.max(4, rect.left) + 'px';
    }

    function postOrder() {
        var root = document.querySelector('.builder-edit-root');
        var els = root ? root.querySelectorAll('.builder-live-block') : document.querySelectorAll('.builder-live-block');
        var order = [];
        els.forEach(function (el) { order.push(parseInt(el.getAttribute('data-block-index'), 10)); });
        post({ type: 'builderReorder', order: order });
    }

    function onDragStart(e) {
        dragEl = this.parentNode; // المنت بلاک
        try { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', this.getAttribute('data-block-index')); } catch (_) {}
        if (dragEl) dragEl.style.opacity = '0.4';
    }
    function onDragOver(e) {
        if (dragEl && dragEl !== this) { e.preventDefault(); this.classList.add('builder-drop-target'); }
    }
    function onDragLeave(e) {
        this.classList.remove('builder-drop-target');
    }
    function onDrop(e) {
        if (!dragEl) return;
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('builder-drop-target');
        var parent = this.parentNode;
        if (parent && dragEl !== this) {
            var rect = this.getBoundingClientRect();
            var before = (e.clientY < rect.top + rect.height / 2);
            if (before) parent.insertBefore(dragEl, this);
            else parent.insertBefore(dragEl, this.nextSibling);
        }
    }
    function onDragEnd(e) {
        if (dragEl) dragEl.style.opacity = '';
        document.querySelectorAll('.builder-drop-target').forEach(function (b) { b.classList.remove('builder-drop-target'); });
        dragEl = null;
        postOrder();
    }

    function init() {
        var root = document.querySelector('.builder-edit-root');
        var blocks = root ? root.querySelectorAll('[data-block-index]') : document.querySelectorAll('[data-block-index]');
        blocks.forEach(function (el) {
            var index = parseInt(el.getAttribute('data-block-index'), 10);
            if (isNaN(index)) return;
            el.classList.add('builder-live-block');
            el.addEventListener('click', function (e) {
                e.stopPropagation();
                selectBlock(index, el);
            });
            el.addEventListener('dragover', onDragOver);
            el.addEventListener('dragleave', onDragLeave);
            el.addEventListener('drop', onDrop);
            // دستهٔ درگ برای جابجایی بلاک (بدون تداخل با ویرایش متن)
            var handle = document.createElement('div');
            handle.className = 'builder-drag-handle';
            handle.title = 'جابجایی';
            handle.innerHTML = '<i class="fa-solid fa-grip-vertical"></i>';
            handle.setAttribute('draggable', 'true');
            handle.addEventListener('dragstart', onDragStart);
            handle.addEventListener('dragend', onDragEnd);
            el.insertBefore(handle, el.firstChild);
            // اولین عنصر متنی را برای ویرایش درجا آماده کن
            var txt = el.querySelector('h1, h2, h3, h4, p, .builder-text');
            if (txt) makeEditable(txt, index);
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.builder-live-block') && !e.target.closest('.builder-inline-toolbar')) {
                selected = -1;
                document.querySelectorAll('.builder-live-block').forEach(function (b) { b.classList.remove('builder-selected'); });
                if (toolbar) toolbar.style.display = 'none';
            }
        });

        post({ type: 'builderReady' });
    }

    window.addEventListener('message', function (e) {
        if (e.origin !== window.location.origin) return;
        var d = e.data || {};
        if (d._ns !== 'builderInline') return;
        if (d.type === 'builderSetContentFields') {
            contentMap = d.fields || {};
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

