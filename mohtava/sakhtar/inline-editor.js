/* =====================================================================
   صفحه‌ساز زنده — موتور ویرایش درون iframe (نگارش ۲ — سبک المنتور)
   - کلیک: انتخاب بلاک + تولبار شناور
   - دابل‌کلیک روی متن: ویرایش مستقیم (contenteditable)
   - درگ خطی: جابجایی بین بلاکها با خط راهنما
   - درگ آزاد: جابجایی/تغییر اندازه با مختصات (بلاکهای free)
   - دریافت بلاک از پالت والد با HTML5 Drag&Drop بین‌سندیه
   ارتباط با والد فقط با postMessage (_ns = builderInline)
   ===================================================================== */

(function () {
    'use strict';

    /* ---------------- وضعیت ---------------- */
    var contentFieldMap = {};
    var selected = -1;
    var selectedEl = null;
    var toolbar = null;
    var hud = null;
    var dropLine = null;
    var editEl = null;
    var internalDragIdx = -1;
    var paletteOver = false;

    function $(s, r) { return (r || document).querySelector(s); }
    function $$(s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); }
    function post(msg) { msg._ns = 'builderInline'; try { window.parent.postMessage(msg, window.location.origin); } catch (e) {} }
    function debounce(fn, ms) { var t; return function () { var a = arguments, c = this; clearTimeout(t); t = setTimeout(function(){ fn.apply(c,a); }, ms); }; }

    /* فقط بلاکهای «محتوای اصلی» صفحه؛ هدر/فوترِ قالب (که آنها هم data-block-index
       دارند) از محاسبات ایندکس کنار گذاشته میشوند تا جایگذاری به انتها نیفتد */
    function rootEl() {
        return $('.builder-edit-root') || document.body;
    }
    function wrappers() { return $$('[data-block-index]', rootEl()); }
    function wrapperOf(i) {
        var ws = wrappers();
        for (var k = 0; k < ws.length; k++) {
            if (parseInt(ws[k].getAttribute('data-block-index'), 10) === i) return ws[k];
        }
        return null;
    }
    function canvasRoot(el) {
        return el ? (el.closest('.builder-free-canvas') || rootEl()) : rootEl();
    }

    /* ---------------- تزئین بلاکها ---------------- */
    function decorate(w) {
        if (!w || w._pbDone) return;
        w._pbDone = true;
        w.classList.add('builder-live-block');
        /* محتوا را در یک کانتینر بپیچ تا جای هندلها امن باشد */
        if (!w.querySelector(':scope > .pb-content')) {
            var c = document.createElement('div');
            c.className = 'pb-content';
            while (w.firstChild) c.appendChild(w.firstChild);
            w.appendChild(c);
        }
        var h = document.createElement('div');
        h.className = 'builder-drag-handle';
        h.title = 'جابجایی';
        h.innerHTML = '<i class="fa-solid fa-grip-vertical"></i>';
        h.draggable = true;
        w.insertBefore(h, w.firstChild);

        var rz = document.createElement('div');
        rz.className = 'builder-resize-handle';
        rz.title = 'تغییر عرض';
        w.appendChild(rz);
    }

    /* ---------------- تولبار شناور ---------------- */
    function ensureToolbar() {
        if (toolbar) return toolbar;
        toolbar = document.createElement('div');
        toolbar.className = 'builder-inline-toolbar';
        toolbar.innerHTML =
            '<button data-act="set" title="تنظیمات بلاک"><i class="fa-solid fa-sliders"></i><span>تنظیمات</span></button>' +
            '<button data-act="img" title="تصویر"><i class="fa-solid fa-image"></i><span>تصویر</span></button>' +
            '<button data-act="dup" title="تکثیر"><i class="fa-regular fa-clone"></i><span>تکثیر</span></button>' +
            '<button data-act="up" title="بالا"><i class="fa-solid fa-arrow-up"></i></button>' +
            '<button data-act="down" title="پایین"><i class="fa-solid fa-arrow-down"></i></button>' +
            '<button data-act="free" title="موقعیت آزاد / خروج از آزاد"><i class="fa-solid fa-crosshairs"></i><span>آزاد</span></button>' +
            '<button data-act="del" title="حذف" class="danger"><i class="fa-solid fa-trash"></i><span>حذف</span></button>';
        document.body.appendChild(toolbar);
        toolbar.addEventListener('click', function (e) {
            var btn = e.target.closest('button');
            if (!btn || selected < 0) return;
            e.stopPropagation();
            var act = btn.dataset.act;
            if (act === 'set') post({ type: 'builderSelect', index: selected });
            else if (act === 'img') post({ type: 'builderOpenImages', index: selected });
            else if (act === 'dup') post({ type: 'builderDuplicate', index: selected });
            else if (act === 'up') post({ type: 'builderMove', index: selected, dir: 'up' });
            else if (act === 'down') post({ type: 'builderMove', index: selected, dir: 'down' });
            else if (act === 'del') post({ type: 'builderDelete', index: selected });
            else if (act === 'free') {
                var w = wrapperOf(selected);
                var isFree = w && getComputedStyle(w).position === 'absolute';
                if (isFree) post({ type: 'builderToggleFree', index: selected, on: false });
                else requestFreeOn(selected);
            }
        });
        return toolbar;
    }

    function showToolbar() {
        var t = ensureToolbar();
        var w = wrapperOf(selected);
        if (!w) { t.style.display = 'none'; return; }
        var freeBtn = t.querySelector('[data-act="free"]');
        freeBtn.classList.toggle('on', getComputedStyle(w).position === 'absolute');
        t.style.display = 'flex';
        var r = w.getBoundingClientRect();
        var top = Math.max(4, r.top - 42);
        var left = Math.max(4, Math.min(window.innerWidth - t.offsetWidth - 4, r.left));
        t.style.top = top + 'px';
        t.style.left = left + 'px';
    }
    function hideToolbar() { if (toolbar) toolbar.style.display = 'none'; }

    /* ---------------- انتخاب ---------------- */
    function select(index) {
        selected = index;
        selectedEl = wrapperOf(index);
        exitEditMode();
        $$('.builder-live-block').forEach(function (b) { b.classList.remove('builder-selected'); });
        if (selectedEl) {
            selectedEl.classList.add('builder-selected');
            showToolbar();
        } else hideToolbar();
        post({ type: 'builderSelect', index: index });
    }
    function deselect() {
        selected = -1; selectedEl = null;
        $$('.builder-live-block').forEach(function (b) { b.classList.remove('builder-selected'); });
        hideToolbar();
    }

    /* ---------------- HUD مختصات ---------------- */
    function ensureHud() {
        if (hud) return hud;
        hud = document.createElement('div');
        hud.className = 'builder-pos-hud';
        document.body.appendChild(hud);
        return hud;
    }
    function showHud(x, y, txt) { var h = ensureHud(); h.style.display = 'block'; h.style.left = (x + 14) + 'px'; h.style.top = (y + 14) + 'px'; h.textContent = txt; }
    function hideHud() { if (hud) hud.style.display = 'none'; }

    /* ---------------- ویرایش متنی درجا ---------------- */
    function startInlineEdit(el, blockIndex) {
        if (editEl) exitEditMode();
        el.setAttribute('contenteditable', 'true');
        el.classList.add('builder-editing-text');
        try { el.focus(); } catch (e) {}
        var push = debounce(function () {
            post({ type: 'builderContent', index: blockIndex, key: (el.getAttribute('data-field') || 'text'), value: el.innerHTML });
        }, 350);
        function onInput() { push(); }
        function onKey(e) {
            if ((e.ctrlKey || e.metaKey)) {
                if (e.key === 'b') { e.preventDefault(); document.execCommand('bold'); }
                else if (e.key === 'i') { e.preventDefault(); document.execCommand('italic'); }
                else if (e.key === 'u') { e.preventDefault(); document.execCommand('underline'); }
                else if (e.key === 's') { e.preventDefault(); }
            }
            if (e.key === 'Escape') { e.stopPropagation(); exitEditMode(); }
        }
        el.addEventListener('input', onInput);
        el.addEventListener('keydown', onKey);
        editEl = { el: el, index: blockIndex, onInput: onInput, onKey: onKey };
    }
    function exitEditMode() {
        if (!editEl) return;
        editEl.el.removeEventListener('input', editEl.onInput);
        editEl.el.removeEventListener('keydown', editEl.onKey);
        editEl.el.removeAttribute('contenteditable');
        editEl.el.classList.remove('builder-editing-text');
        editEl = null;
    }

    /* ---------------- خط راهنمای درج ---------------- */
    function ensureLine() {
        if (!dropLine) { dropLine = document.createElement('div'); dropLine.className = 'builder-drop-line'; }
        return dropLine;
    }
    function removeLine() { if (dropLine && dropLine.parentNode) dropLine.parentNode.removeChild(dropLine); }
    /* ایندکس درج بر اساس موقعیت موس؛ اگر خط قبل از المان i است همان i وگرنه i+1 */
    function insertionInfo(clientY) {
        var ws = wrappers();
        for (var i = 0; i < ws.length; i++) {
            var r = ws[i].getBoundingClientRect();
            var mid = r.top + r.height / 2;
            if (clientY < mid) return { index: i, before: ws[i] };
        }
        return { index: ws.length, before: null };
    }
    function showLineAt(clientY) {
        var info = insertionInfo(clientY);
        var line = ensureLine();
        if (info.before) info.before.parentNode.insertBefore(line, info.before);
        else canvasRoot(wrappers()[0]).appendChild(line);
        return info.index;
    }

    /* ---------------- درگ آزاد (move/resize) ---------------- */
    var freeDrag = null;
    function beginFreeDrag(e, mode) {
        var w = wrapperOf(selected);
        if (!w) return;
        var st = getComputedStyle(w);
        if (st.position !== 'absolute') return;
        var root = canvasRoot(w);
        var rr = root.getBoundingClientRect();
        var wr = w.getBoundingClientRect();
        var scale = rr.width ? 1 : 1; /* مقیاس بیرونی روی مختصات داخلی اثر ندارد */
        freeDrag = {
            el: w, root: root, mode: mode,
            sx: e.clientX, sy: e.clientY,
            ox: parseFloat(w.style.left || wr.left - rr.left) || 0,
            oy: parseFloat(w.style.top || wr.top - rr.top) || 0,
            ow: wr.width,
            z: parseInt(st.zIndex, 10) || 1
        };
        w.style.transition = 'none';
        document.body.classList.add('pb-no-sel');
        e.preventDefault();
    }
    window.addEventListener('mousemove', function (e) {
        if (!freeDrag) return;
        var dx = e.clientX - freeDrag.sx, dy = e.clientY - freeDrag.sy;
        var w = freeDrag.el;
        if (freeDrag.mode === 'move') {
            w.style.left = Math.round(freeDrag.ox + dx) + 'px';
            w.style.top = Math.round(freeDrag.oy + dy) + 'px';
        } else {
            var nw = Math.max(80, Math.round(freeDrag.ow - dx));
            w.style.width = nw + 'px';
        }
        showHud(e.clientX, e.clientY,
            'x:' + parseInt(w.style.left,10) + ' y:' + parseInt(w.style.top,10) + ' w:' + parseInt(w.style.width,10));
    });
    window.addEventListener('mouseup', function () {
        if (!freeDrag) return;
        var w = freeDrag.el;
        var root = freeDrag.root;
        var rr = root.getBoundingClientRect();
        var wr = w.getBoundingClientRect();
        var pos = {
            x: Math.round(wr.left - rr.left),
            y: Math.round(wr.top - rr.top),
            w: Math.round(wr.width),
            z: parseInt(getComputedStyle(w).zIndex, 10) || 1
        };
        w.style.transition = '';
        var idx = selected;
        freeDrag = null;
        hideHud();
        document.body.classList.remove('pb-no-sel');
        post({ type: 'builderPos', index: idx, pos: pos });
    });

    function requestFreeOn(index) {
        var w = wrapperOf(index);
        if (!w) return;
        /* موقعیت اولیه = جای فعلی نسبت به ریشه؛ والد با initPos آزاد میکند */
        var root = canvasRoot(w);
        var rr = root.getBoundingClientRect();
        var wr = w.getBoundingClientRect();
        var pos = { x: Math.round(wr.left - rr.left), y: Math.round(wr.top - rr.top), w: Math.round(wr.width), z: wrappers().length + 1 };
        post({ type: 'builderToggleFree', index: index, on: true, initPos: pos });
    }

    /* ---------------- درگ داخلی (جابجایی ترتیب) ---------------- */
    function onGripStart(e) {
        var w = e.target.closest('[data-block-index]');
        if (!w) return;
        var st = getComputedStyle(w);
        if (st.position === 'absolute') { /* بلاک آزاد: درگ خطی نه — حرکت آزاد از خود بلاک */ }
        internalDragIdx = parseInt(w.getAttribute('data-block-index'), 10);
        try {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', String(internalDragIdx));
            e.dataTransfer.setData('text/x-builder-internal', '1');
        } catch (err) {}
        document.body.classList.add('pb-is-dragging');
    }
    function isOurDrag(e) {
        var t = e.dataTransfer && e.dataTransfer.types;
        if (!t) return false;
        for (var i = 0; i < t.length; i++) {
            if (t[i] === 'text/x-builder-block' || t[i] === 'text/x-builder-internal') return true;
        }
        return false;
    }
    document.addEventListener('dragstart', function (e) {
        if (e.target.classList && e.target.classList.contains('builder-drag-handle')) onGripStart(e);
    });
    document.addEventListener('dragover', function (e) {
        if (!isOurDrag(e)) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = internalDragIdx >= 0 ? 'move' : 'copy';
        if (!paletteOver && internalDragIdx >= 0) { /* نمایش خط برای درگ داخلی هم */ }
        showLineAt(e.clientY);
        if (e.altKey) removeLine();
    });
    document.addEventListener('dragleave', function (e) {
        if (!isOurDrag(e)) return;
        if (e.relatedTarget === null || e.clientX <= 0 || e.clientY <= 0) removeLine();
    });
    document.addEventListener('drop', function (e) {
        if (!isOurDrag(e)) return;
        e.preventDefault();
        removeLine();
        document.body.classList.remove('pb-is-dragging');
        var alt = e.altKey;
        if (internalDragIdx >= 0) {
            /* جابجایی ترتیب داخلی */
            var from = internalDragIdx;
            var info = insertionInfo(e.clientY);
            var to = info.index;
            if (alt) {
                /* انتقال به حالت آزاد در نقطه فعلی */
                var w = wrapperOf(from);
                var root = canvasRoot(w);
                var rr = root.getBoundingClientRect();
                var wr = w.getBoundingClientRect();
                post({
                    type: 'builderToggleFree', index: from, on: true,
                    initPos: { x: Math.round(e.clientX - rr.left - wr.width / 2), y: Math.round(e.clientY - rr.top - 12), w: Math.round(wr.width), z: wrappers().length + 1 }
                });
            } else {
                var order = [];
                for (var i = 0; i < wrappers().length; i++) order.push(i);
                order.splice(from, 1);
                var ins = (to > from) ? to - 1 : to;
                order.splice(ins, 0, from);
                post({ type: 'builderReorder', order: order });
            }
            internalDragIdx = -1;
            return;
        }
        /* بلاک جدید از پالت والد */
        var btype = null;
        try { btype = e.dataTransfer.getData('text/x-builder-block'); } catch (err) {}
        if (!btype) return;
        if (alt) {
            var root2 = canvasRoot(wrappers()[0]);
            var rr2 = root2.getBoundingClientRect();
            post({ type: 'builderInsertFree', btype: btype, pos: { x: Math.round(e.clientX - rr2.left - 150), y: Math.round(e.clientY - rr2.top - 10), w: 300, z: wrappers().length + 1 } });
        } else {
            var idx = insertionInfo(e.clientY).index;
            post({ type: 'builderInsertAt', btype: btype, index: idx });
        }
    });
    document.addEventListener('dragend', function () {
        internalDragIdx = -1;
        removeLine();
        document.body.classList.remove('pb-is-dragging');
    });

    /* ---------------- رویدادهای عمومی ---------------- */
    document.addEventListener('mouseover', function (e) {
        var b = e.target.closest('[data-block-index]');
        $$('.builder-live-block').forEach(function (x) { if (b !== x) x.classList.remove('pb-hover'); });
        if (b) b.classList.add('pb-hover');
    });

    document.addEventListener('click', function (e) {
        /* جلوگیری از باز شدن لینکها در حالت ویرایش */
        var a = e.target.closest('a[href]');
        if (a && !a.classList.contains('builder-inline-toolbar')) {
            var inBlock = !!e.target.closest('[data-block-index]');
            if (inBlock) e.preventDefault();
        }
        if (e.target.closest('.builder-inline-toolbar')) return;
        var blk = e.target.closest('[data-block-index]');
        if (!blk) { deselect(); return; }
        if (editEl && e.target !== editEl.el && !editEl.el.contains(e.target)) exitEditMode();
        var idx = parseInt(blk.getAttribute('data-block-index'), 10);
        if (idx !== selected) select(idx);
        showToolbar();
    });

    document.addEventListener('dblclick', function (e) {
        var blk = e.target.closest('[data-block-index]');
        if (!blk) return;
        e.preventDefault(); e.stopPropagation();
        var idx = parseInt(blk.getAttribute('data-block-index'), 10);
        var txt = e.target.closest('.builder-editable');
        if (!txt || !txt.classList.contains('builder-text')) {
            txt = blk.querySelector('.builder-text.builder-editable');
        }
        if (txt) { select(idx); startInlineEdit(txt, idx); }
    });

    /* تغییر اندازه (دسته گوشه) */
    document.addEventListener('mousedown', function (e) {
        if (e.target.classList && e.target.classList.contains('builder-resize-handle')) {
            if (selected >= 0) beginFreeDrag(e, 'resize');
        } else if (selected >= 0) {
            var w = wrapperOf(selected);
            var isFree = w && getComputedStyle(w).position === 'absolute';
            var onHandleOrToolbar = e.target.closest('.builder-drag-handle, .builder-inline-toolbar, .builder-resize-handle');
            var onEditableText = e.target.closest('.builder-editable.builder-text');
            if (isFree && !onHandleOrToolbar && !onEditableText && !e.target.closest('a,button,input,select,textarea,iframe')) {
                beginFreeDrag(e, 'move');
            }
        }
    });

    /* ---------------- پیامهای والد ---------------- */
    function reindex() {
        wrappers().forEach(function (w, i) {
            w.setAttribute('data-block-index', i);
            if (!/(^|\s)bpos-/.test(w.className)) { /* noop */ }
            w.classList.remove.apply(w.classList, Array.prototype.filter.call(w.classList, function(c){ return c.indexOf('bpos-') === 0; }));
            w.classList.add('bpos-' + i);
        });
    }
    function syncSelectionAfterChange() {
        if (selected >= 0) {
            var w = wrapperOf(selected);
            if (w) { selectedEl = w; showToolbar(); } else deselect();
        }
    }

    window.addEventListener('message', function (e) {
        if (e.origin !== window.location.origin) return;
        var d = e.data || {};
        if (d._ns !== 'builderInline') return;
        switch (d.type) {
            case 'builderSetContentFields':
                if (d.fields) for (var k in d.fields) contentFieldMap[k] = d.fields[k];
                break;
            case 'builderFocusBlock':
                if (d.index >= 0) select(d.index); else deselect();
                break;
            case 'builderExit':
                exitEditMode(); deselect();
                break;
            case 'builderEditBlock':
                if (selectedEl) {
                    var txt = selectedEl.querySelector('.builder-text.builder-editable') || selectedEl.querySelector('.builder-editable');
                    if (txt) startInlineEdit(txt, selected);
                }
                break;
            case 'builderSetBlockHtml': {
                var w = wrapperOf(d.index);
                if (w) {
                    var c = w.querySelector(':scope > .pb-content');
                    if (c) c.innerHTML = d.html || ''; else w.innerHTML = d.html || '';
                    decorate(w);
                    syncSelectionAfterChange();
                }
                break;
            }
            case 'builderInsertHtml': {
                var tmp = document.createElement('div');
                tmp.innerHTML = d.html || '';
                var nw = tmp.firstElementChild;
                if (!nw) break;
                var ws = wrappers();
                if (d.at >= 0 && d.at < ws.length) ws[d.at].parentNode.insertBefore(nw, ws[d.at]);
                else {
                    var rEl = ws.length ? ws[ws.length - 1].parentNode : rootEl();
                    rEl.appendChild(nw);
                }
                decorate(nw);
                reindex();
                syncSelectionAfterChange();
                break;
            }
            case 'builderRemoveBlockDom': {
                var rw = wrapperOf(d.index);
                if (rw) rw.parentNode.removeChild(rw);
                reindex();
                deselect();
                break;
            }
            case 'builderReorderDom': {
                var order = d.order || [];
                var cur = wrappers();
                var map = {};
                cur.forEach(function (w) { map[w.getAttribute('data-block-index')] = w; });
                var parent = cur.length ? cur[0].parentNode : null;
                if (!parent) break;
                for (var i = 0; i < order.length; i++) {
                    var el = map[String(order[i])];
                    if (el) parent.appendChild(el);
                }
                reindex();
                syncSelectionAfterChange();
                break;
            }
            case 'builderApplyPosCss': {
                var old = $('style.builder-pos-css');
                if (old) old.parentNode.removeChild(old);
                if (d.css) {
                    var holder = rootEl();
                    var tmp2 = document.createElement('div');
                    tmp2.innerHTML = d.css;
                    holder.insertBefore(tmp2.firstElementChild, holder.firstChild);
                }
                break;
            }
            case 'builderDeviceChanged': break;
            case 'builderPosChanged': break;
            case 'builderDoFree': requestFreeOn(d.index); break;

            /* ---------- درگ بلاک از پالت والد (سفارشی) ---------- */
            case 'builderDragMove': {
                if (!d.inside || d.alt) { removeLine(); break; }
                showLineAt(d.y);
                break;
            }
            case 'builderDragDrop': {
                removeLine();
                document.body.classList.remove('pb-is-dragging');
                if (!d.inside) break;
                if (d.alt) {
                    var froot = canvasRoot(wrappers()[0]);
                    var frr = froot.getBoundingClientRect();
                    post({
                        type: 'builderInsertFree', btype: d.btype,
                        pos: { x: Math.round(d.x - 150), y: Math.round(d.y - 10), w: 300, z: wrappers().length + 1 }
                    });
                } else {
                    var didx = insertionInfo(d.y).index;
                    post({ type: 'builderInsertAt', btype: d.btype, index: didx });
                }
                break;
            }
            case 'builderDragCancel': {
                removeLine();
                document.body.classList.remove('pb-is-dragging');
                break;
            }
        }
    });

    /* ---------------- شروع ---------------- */
    function init() {
        wrappers().forEach(decorate);
        post({ type: 'builderReady' });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
})();
