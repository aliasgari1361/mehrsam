<?php
/**
 * دکمه پشتیبانی شناور — همه کانالهای تماس مشتری در یک ویجت
 * تنظیمات: پنل ← تنظیمات ← پیام‌ها (site_settings.json ← support)
 * چت زنده از ویجت موجود (chat/widget.php) باز میشود.
 */
global $site_settings;
$sup_cfg = is_array($site_settings ?? null) ? ($site_settings['support'] ?? []) : [];
if (isset($sup_cfg['enabled']) && !$sup_cfg['enabled']) return;

$sup_main = $sup_cfg['main_color'] ?? '#FF6F00';
$sup_fs = (int)($sup_cfg['font_size'] ?? 14);
$sup_ch = $sup_cfg['channels'] ?? [];
$sup_on = function ($ch) use ($sup_ch) { return !empty($sup_ch[$ch]['on']) && trim((string)($sup_ch[$ch]['v'] ?? '')) !== ''; };
$sup_v = function ($ch) use ($sup_ch) { return trim((string)($sup_ch[$ch]['v'] ?? '')); };
$sup_c = function ($ch, $def) use ($sup_ch) { return $sup_ch[$ch]['color'] ?? $def; };

$sup_items = [];
if ($sup_on('telegram')) $sup_items[] = ['تلگرام', 'fa-brands fa-telegram', $sup_c('telegram', '#0088cc'), 'https://t.me/' . ltrim($sup_v('telegram'), '@'), '_blank'];
if ($sup_on('eitaa')) $sup_items[] = ['ایتا', 'fa-solid fa-comments', $sup_c('eitaa', '#E94560'), 'https://eitaa.com/' . ltrim($sup_v('eitaa'), '@'), '_blank'];
if ($sup_on('rubika')) $sup_items[] = ['روبیکا', 'fa-solid fa-comment-dots', $sup_c('rubika', '#5F4B8B'), 'https://rubika.ir/' . ltrim($sup_v('rubika'), '@'), '_blank'];
if ($sup_on('whatsapp')) $sup_items[] = ['واتس‌اپ', 'fa-brands fa-whatsapp', $sup_c('whatsapp', '#25d366'), 'https://wa.me/' . preg_replace('/\D/', '', $sup_v('whatsapp')), '_blank'];
if ($sup_on('email')) $sup_items[] = ['ایمیل', 'fa-solid fa-envelope', $sup_c('email', '#EA4335'), 'mailto:' . $sup_v('email'), '_self'];
if ($sup_on('sms')) $sup_items[] = ['پیامک', 'fa-solid fa-comment-sms', $sup_c('sms', '#16a085'), 'sms:' . preg_replace('/\D/', '', $sup_v('sms')), '_self'];
if ($sup_on('tel')) $sup_items[] = ['تماس تلفنی', 'fa-solid fa-phone', $sup_c('tel', '#2D3436'), 'tel:' . preg_replace('/\D/', '', $sup_v('tel')), '_self'];
/* چت زنده همیشه اول */
$has_chat = true;
?>
<style>
    .sup-wrap { position:fixed; bottom:24px; right:24px; z-index:9000; font-family:inherit; }
    .sup-btn { width:56px; height:56px; border-radius:50%; border:none; cursor:pointer; background:<?= $sup_main ?>; color:#fff; font-size:24px; box-shadow:0 6px 22px rgba(0,0,0,.22); display:flex; align-items:center; justify-content:center; transition:transform .2s, box-shadow .2s; }
    .sup-btn:hover { transform:scale(1.07); box-shadow:0 10px 28px rgba(0,0,0,.3); }
    .sup-panel { position:absolute; bottom:68px; right:0; width:230px; background:#fff; border-radius:16px; box-shadow:0 14px 40px rgba(0,0,0,.22); padding:10px; display:none; font-size:<?= $sup_fs ?>px; }
    .sup-panel.open { display:block; animation:supIn .18s ease; }
    @keyframes supIn { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:none; } }
    .sup-panel .sup-head { text-align:center; font-weight:800; color:#333; padding:6px 0 10px; font-size:<?= $sup_fs + 1 ?>px; }
    .sup-item { display:flex; align-items:center; gap:10px; padding:11px 12px; border-radius:10px; color:#fff !important; text-decoration:none !important; margin-bottom:7px; font-weight:700; transition:transform .12s, filter .12s; background:var(--sc,#333); }
    .sup-item:hover { transform:translateX(-3px); filter:brightness(1.08); }
    .sup-item i { font-size:<?= $sup_fs + 4 ?>px; }
    @media (max-width:767px) { .sup-wrap { bottom:18px; right:18px; } .sup-btn { width:50px; height:50px; font-size:21px; } }
</style>

<div class="sup-wrap">
    <div class="sup-panel" id="supPanel">
        <div class="sup-head">💬 پشتیبانی آنلاین</div>
        <?php if ($has_chat): ?>
        <button type="button" class="sup-item" style="--sc:<?= $sup_main ?>;width:100%;font-family:inherit;font-size:inherit;" onclick="supOpenChat()">
            <i class="fa-solid fa-comment"></i> چت آنلاین
        </button>
        <?php endif; ?>
        <?php foreach ($sup_items as $it): ?>
        <a class="sup-item" style="--sc:<?= htmlspecialchars($it[2]) ?>" href="<?= htmlspecialchars($it[3]) ?>" target="<?= $it[4] ?>" rel="noopener">
            <i class="<?= $it[1] ?>"></i> <?= htmlspecialchars($it[0]) ?>
        </a>
        <?php endforeach; ?>
    </div>
    <button type="button" class="sup-btn" id="supBtn" title="پشتیبانی" onclick="supToggle(event)">
        <i class="fa-solid fa-headset"></i>
    </button>
</div>

<script>
function supToggle(e) {
    if (e) e.stopPropagation();
    document.getElementById('supPanel').classList.toggle('open');
}
function supOpenChat() {
    document.getElementById('supPanel').classList.remove('open');
    if (typeof chatToggle === 'function') { chatToggle(); return; }
    var b = document.getElementById('chatBtn');
    if (b) b.click();
}
document.addEventListener('click', function (e) {
    var p = document.getElementById('supPanel');
    if (!p) return;
    if (!e.target.closest('.sup-wrap')) p.classList.remove('open');
});
</script>
