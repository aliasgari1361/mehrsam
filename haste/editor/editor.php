<?php
$edr_name = $edr_name ?? 'content';
$edr_id = $edr_id ?? 'edrArea';
$edr_value = $edr_value ?? '';
?>
<style>
.edr-wrap { display:flex; flex-direction:column; gap:0; border:1.5px solid #dde1e6; border-radius:10px; overflow:hidden; background:#fff; }
.edr-tbar { display:flex; flex-wrap:wrap; gap:2px; padding:6px 8px; background:#f8f9fa; border-bottom:1px solid #eef0f4; align-items:center; user-select:none; }
.edr-tbar .edr-grp { display:flex; gap:1px; align-items:center; padding:0 3px; }
.edr-tbar .edr-grp + .edr-grp { border-right:1px solid #dde1e6; margin-right:3px; padding-right:6px; }
.edr-tbar button { background:none; border:1px solid transparent; border-radius:6px; padding:4px 8px; cursor:pointer; font-size:13px; color:#333; line-height:1; transition:all .15s; white-space:nowrap; min-width:28px; }
.edr-tbar button:hover { background:#e9ecef; border-color:#ced4da; }
.edr-tbar button:active { background:#dee2e6; }
.edr-tbar button.edr-on { background:#d3e3fd; border-color:#86b7fe; color:#0a58ca; }
.edr-tbar select { padding:3px 5px; border:1px solid #dde1e6; border-radius:6px; font-size:12px; background:#fff; cursor:pointer; }
.edr-tbar select:hover { border-color:#adb5bd; }
.edr-tbar input[type=color] { width:22px; height:22px; padding:0; border:1px solid #dde1e6; border-radius:4px; cursor:pointer; background:none; vertical-align:middle; }
.edr-body { position:relative; }
.edr-body textarea { width:100%; border:none; outline:none; padding:16px; font-family:'Vazir','Tahoma',monospace; font-size:14px; line-height:1.8; resize:vertical; direction:rtl; min-height:300px; box-sizing:border-box; }
.edr-body textarea:focus { box-shadow:inset 0 0 0 1px #86b7fe; }
.edr-body .edr-preview { display:none; padding:16px; font-family:'Vazir','Tahoma',sans-serif; font-size:14px; line-height:1.8; direction:rtl; min-height:300px; background:#fff; overflow-y:auto; }
.edr-body .edr-preview img { max-width:100%; height:auto; border-radius:6px; }
.edr-body .edr-preview table { border-collapse:collapse; width:100%; margin:10px 0; }
.edr-body .edr-preview td, .edr-body .edr-preview th { border:1px solid #dde1e6; padding:8px 12px; text-align:right; }
.edr-body .edr-preview th { background:#f8f9fa; }
.edr-body .edr-preview blockquote { border-right:4px solid #86b7fe; padding:8px 16px; margin:10px 0; background:#f8f9fa; border-radius:4px; color:#555; }
.edr-body .edr-preview pre { background:#1e1e2e; color:#cdd6f4; padding:16px; border-radius:8px; overflow-x:auto; direction:ltr; text-align:left; font-size:13px; line-height:1.5; }
.edr-body .edr-preview code { background:#f0f0f0; padding:2px 6px; border-radius:4px; font-size:13px; direction:ltr; }
.edr-body .edr-preview pre code { background:none; padding:0; }
.edr-fullscreen { position:fixed; inset:0; z-index:9999; background:#fff; display:flex; flex-direction:column; }
.edr-fullscreen .edr-body { flex:1; }
.edr-fullscreen .edr-body textarea { height:100% !important; resize:none; }
.edr-fullscreen .edr-body .edr-preview { height:100% !important; }
.edr-status { display:flex; justify-content:space-between; padding:4px 12px; background:#f8f9fa; border-top:1px solid #eef0f4; font-size:11px; color:#888; }
.edr-status span { direction:ltr; }
@media(max-width:768px){ .edr-tbar button { font-size:11px; padding:3px 5px; min-width:24px; } .edr-tbar select { font-size:11px; } }
</style>

<div class="edr-wrap">
  <div class="edr-tbar">
    <select class="edr-heading" onchange="edrHeading(this)">
      <option value="">پاراگراف</option>
      <option value="h1">هدر ۱</option>
      <option value="h2">هدر ۲</option>
      <option value="h3">هدر ۳</option>
      <option value="h4">هدر ۴</option>
    </select>

    <div class="edr-grp">
      <button type="button" onclick="edrFmt('bold')" title="ضخیم (Ctrl+B)"><b>B</b></button>
      <button type="button" onclick="edrFmt('italic')" title="مورب (Ctrl+I)"><i>I</i></button>
      <button type="button" onclick="edrFmt('underline')" title="زیرخط (Ctrl+U)"><u>U</u></button>
      <button type="button" onclick="edrFmt('strike')" title="خط خورده"><s>S</s></button>
    </div>

    <div class="edr-grp">
      <button type="button" onclick="edrFmt('ul')" title="لیست نامرتب">• فهرست</button>
      <button type="button" onclick="edrFmt('ol')" title="لیست مرتب">۱. فهرست</button>
      <button type="button" onclick="edrFmt('blockquote')" title="نقل قول">❝ نقل قول</button>
      <button type="button" onclick="edrFmt('code')" title="کد">&lt;/&gt; کد</button>
    </div>

    <div class="edr-grp">
      <button type="button" onclick="edrAlign('right')" title="راست‌چین">⫸</button>
      <button type="button" onclick="edrAlign('center')" title="وسط‌چین">⫯</button>
      <button type="button" onclick="edrAlign('left')" title="چپ‌چین">⫷</button>
      <button type="button" onclick="edrAlign('justify')" title="تراز کامل">⟺</button>
    </div>

    <div class="edr-grp">
      <button type="button" onclick="edrDir('rtl')" title="راست به چپ">→ راست‌نویس</button>
      <button type="button" onclick="edrDir('ltr')" title="چپ به راست">← چپ‌نویس</button>
    </div>

    <div class="edr-grp">
      <button type="button" onclick="edrLink()" title="لینک">🔗 لینک</button>
      <button type="button" onclick="edrImage()" title="تصویر">🖼 تصویر</button>
      <button type="button" onclick="edrTable()" title="جدول">⊞ جدول</button>
      <button type="button" onclick="edrHr()" title="خط افقی">— خط</button>
    </div>

    <div class="edr-grp">
      <input type="color" onchange="edrColor('color',this.value)" title="رنگ متن" value="#000000">
      <input type="color" onchange="edrColor('bg',this.value)" title="رنگ پس‌زمینه" value="#ffff00">
    </div>

    <div class="edr-grp">
      <button type="button" onclick="edrUndo()" title="بازگشت (Ctrl+Z)">↩ واگرد</button>
      <button type="button" onclick="edrRedo()" title="بازگشت مجدد (Ctrl+Shift+Z)">↪ جلوگرد</button>
      <button type="button" onclick="edrClear()" title="پاک کردن قالب‌بندی">✕ پاک</button>
    </div>

    <div class="edr-grp">
      <button type="button" onclick="edrPreview()" title="پیش‌نمایش">👁 پیش‌نمایش</button>
      <button type="button" onclick="edrFullscreen()" id="<?= $edr_id ?>FsBtn" title="تمام صفحه (F11)">⛶ تمام صفحه</button>
    </div>
  </div>

  <div class="edr-body" id="<?= $edr_id ?>Body">
    <textarea name="<?= $edr_name ?>" id="<?= $edr_id ?>" oninput="edrOnInput()" onkeydown="edrShortcut(event)"><?= $edr_value ?></textarea>
    <div class="edr-preview" id="<?= $edr_id ?>Preview"></div>
  </div>

  <div class="edr-status">
    <span id="<?= $edr_id ?>Words">۰ کلمه</span>
    <span id="<?= $edr_id ?>Chars">۰ کاراکتر</span>
  </div>

  <input type="file" accept="image/*" id="<?= $edr_id ?>File" style="display:none" onchange="edrImageUpload(this)">
</div>

<script>
var EDR_ID = '<?= $edr_id ?>';

function edrArea() {
  return document.getElementById(EDR_ID);
}

function edrGet() {
  var ta = edrArea();
  if (!ta) return {ta: null, start: 0, end: 0, sel: ''};
  var start = ta.selectionStart, end = ta.selectionEnd;
  return {ta: ta, start: start, end: end, sel: ta.value.substring(start, end)};
}

function edrWrap(before, after) {
  var g = edrGet();
  if (!g.ta) return;
  var t = g.sel || before;
  g.ta.setRangeText(before + t + after, g.start, g.end, 'select');
  g.ta.focus();
}

function edrWrapBlock(tag) {
  var g = edrGet();
  if (!g.ta) return;
  var t = g.sel || '\u00A0';
  g.ta.setRangeText('<' + tag + '>\n' + t + '\n</' + tag + '>', g.start, g.end, 'select');
  g.ta.focus();
}

function edrFmt(cmd) {
  switch(cmd) {
    case 'bold': edrWrap('<b>', '</b>'); break;
    case 'italic': edrWrap('<i>', '</i>'); break;
    case 'underline': edrWrap('<u>', '</u>'); break;
    case 'strike': edrWrap('<s>', '</s>'); break;
    case 'ul': edrWrapBlock('ul'); break;
    case 'ol': edrWrapBlock('ol'); break;
    case 'blockquote': edrWrapBlock('blockquote'); break;
    case 'code': edrWrap('<code>', '</code>'); break;
  }
}

function edrAlign(a) {
  var g = edrGet();
  if (!g.ta) return;
  g.ta.setRangeText('<div style="text-align:' + a + ';">\n' + (g.sel || '\u00A0') + '\n</div>', g.start, g.end, 'select');
  g.ta.focus();
}

function edrDir(d) {
  var g = edrGet();
  if (!g.ta) return;
  g.ta.setRangeText('<div dir="' + d + '">\n' + (g.sel || '\u00A0') + '\n</div>', g.start, g.end, 'select');
  g.ta.focus();
}

function edrLink() {
  var g = edrGet();
  if (!g.ta) return;
  var url = prompt('آدرس لینک (http://...):');
  if (!url) return;
  var t = g.sel || url;
  g.ta.setRangeText('<a href="' + url.replace(/"/g,'&quot;') + '">' + t + '</a>', g.start, g.end, 'select');
  g.ta.focus();
}

function edrImage() {
  document.getElementById(EDR_ID + 'File').click();
}

function edrImageUpload(inp) {
  var file = inp.files && inp.files[0];
  if (!file) return;
  var g = edrGet();
  if (!g.ta) return;
  var reader = new FileReader();
  reader.onload = function(e) {
    var alt = file.name.replace(/\.[^.]+$/, '');
    g.ta.setRangeText('<img src="' + e.target.result + '" alt="' + alt + '" style="max-width:100%;border-radius:6px;">', g.start, g.end, 'select');
    g.ta.focus();
    inp.value = '';
  };
  reader.readAsDataURL(file);
}

function edrTable() {
  var g = edrGet();
  if (!g.ta) return;
  var rows = parseInt(prompt('تعداد سطر:', '3'));
  if (!rows || rows < 1) return;
  var cols = parseInt(prompt('تعداد ستون:', '3'));
  if (!cols || cols < 1) return;
  var t = '<table>\n';
  for (var r = 0; r < rows; r++) {
    t += '  <tr>\n';
    for (var c = 0; c < cols; c++)
      t += '    <' + (r === 0 ? 'th' : 'td') + '>متن</' + (r === 0 ? 'th' : 'td') + '>\n';
    t += '  </tr>\n';
  }
  t += '</table>';
  g.ta.setRangeText(t, g.start, g.end, 'select');
  g.ta.focus();
}

function edrHr() {
  var g = edrGet();
  if (!g.ta) return;
  g.ta.setRangeText('\n<hr>\n', g.start, g.end, 'select');
  g.ta.focus();
}

function edrColor(type, val) {
  var g = edrGet();
  if (!g.ta) return;
  g.ta.setRangeText('<span style="' + (type === 'color' ? 'color' : 'background-color') + ':' + val + ';">' + (g.sel || '\u00A0') + '</span>', g.start, g.end, 'select');
  g.ta.focus();
}

function edrHeading(sel) {
  var tag = sel.value;
  sel.value = '';
  if (!tag) return;
  var g = edrGet();
  if (!g.ta) return;
  g.ta.setRangeText('<' + tag + '>\n' + (g.sel || '\u00A0') + '\n</' + tag + '>', g.start, g.end, 'select');
  g.ta.focus();
}

var edrUndoStack = [], edrRedoStack = [], edrSaveTimer = null;

function edrSaveState() {
  var ta = edrArea();
  if (!ta) return;
  edrUndoStack.push(ta.value);
  if (edrUndoStack.length > 100) edrUndoStack.shift();
  edrRedoStack = [];
}

function edrUndo() {
  var ta = edrArea();
  if (!ta || edrUndoStack.length < 2) return;
  edrRedoStack.push(edrUndoStack.pop());
  ta.value = edrUndoStack[edrUndoStack.length - 1];
  edrOnInput();
}

function edrRedo() {
  var ta = edrArea();
  if (!ta || !edrRedoStack.length) return;
  edrUndoStack.push(edrRedoStack.pop());
  ta.value = edrUndoStack[edrUndoStack.length - 1];
  edrOnInput();
}

function edrClear() {
  var g = edrGet();
  if (!g.ta) return;
  if (!g.sel) { alert('متنی انتخاب نشده'); return; }
  g.ta.setRangeText(g.sel.replace(/<\/?[^>]+(>|$)/g, ''), g.start, g.end, 'select');
  g.ta.focus();
}

function edrOnInput() {
  var ta = edrArea();
  if (!ta) return;
  clearTimeout(edrSaveTimer);
  edrSaveTimer = setTimeout(edrSaveState, 500);
  var text = ta.value;
  var clean = text.replace(/<[^>]+>/g,' ').trim();
  var words = clean ? clean.split(/\s+/).length : 0;
  document.getElementById(EDR_ID + 'Words').textContent = words.toLocaleString('fa') + ' کلمه';
  document.getElementById(EDR_ID + 'Chars').textContent = text.length.toLocaleString('fa') + ' کاراکتر';
}

function edrPreview() {
  var ta = edrArea();
  if (!ta) return;
  var pv = document.getElementById(EDR_ID + 'Preview');
  if (pv.style.display === 'block') {
    pv.style.display = 'none';
    ta.style.display = 'block';
    return;
  }
  pv.innerHTML = ta.value;
  pv.style.display = 'block';
  ta.style.display = 'none';
}

function edrFullscreen() {
  var btn = document.getElementById(EDR_ID + 'FsBtn');
  if (!btn) return;
  var wrap = btn.closest('.edr-wrap');
  if (!wrap) return;
  wrap.classList.toggle('edr-fullscreen');
  if (wrap.classList.contains('edr-fullscreen')) {
    document.body.style.overflow = 'hidden';
    btn.innerHTML = '✕ خروج';
  } else {
    document.body.style.overflow = '';
    btn.innerHTML = '⛶ تمام صفحه';
  }
}

function edrShortcut(e) {
  if ((e.ctrlKey || e.metaKey) && e.key === 'b') { e.preventDefault(); edrFmt('bold'); }
  if ((e.ctrlKey || e.metaKey) && e.key === 'i') { e.preventDefault(); edrFmt('italic'); }
  if ((e.ctrlKey || e.metaKey) && e.key === 'u') { e.preventDefault(); edrFmt('underline'); }
  if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) { e.preventDefault(); edrUndo(); }
  if ((e.ctrlKey || e.metaKey) && e.key === 'z' && e.shiftKey) { e.preventDefault(); edrRedo(); }
  if ((e.ctrlKey || e.metaKey) && e.key === 'y') { e.preventDefault(); edrRedo(); }
  if (e.key === 'F11') { e.preventDefault(); edrFullscreen(); }
  if (e.key === 'Escape') { var b = document.querySelector('.edr-fullscreen'); if(b) edrFullscreen(); }
}

setTimeout(function(){ edrSaveState(); }, 100);
</script>
