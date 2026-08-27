<?php
/**
 * مدیریت اعلان گوشی (Web Push) — mod/push
 * صفحه فعالسازی + ذخیره اشتراک + ارسال تست
 */

require_once MASIR_RISH . 'afzuneh/elpayaagh/Notifier.php';

function push_route($action, $params) {
    switch ($action) {
        case 'subscribe':
            push_handle_subscribe();
            break;
        case 'test':
            push_handle_test();
            break;
        default:
            push_page();
            break;
    }
}

function push_page() {
    $push = new kanal_push();
    $keys = null;
    /* کلیدها را همینجا هم بساز (اولین بازدید صفحه) */
    $ref = new ReflectionMethod('kanal_push', 'cfg');
    $ref->setAccessible(true);
    $keys = $ref->invoke($push);
    $count = $push->count_subscriptions();
    include __DIR__ . '/../../ghaleb/ghmod/sarfaraz.php';
    ?>
    <style>
        .push-card { max-width:560px; background:#fff; border:1px solid var(--rang-border); border-radius:14px; padding:26px; }
        .push-big-btn { width:100%; padding:18px; font-size:16px; font-weight:800; background:var(--rang-asli,#FF6F00); color:#fff; border:none; border-radius:12px; cursor:pointer; font-family:inherit; transition:all .2s; }
        .push-big-btn:hover { background:var(--rang-tira,#E65100); box-shadow:0 6px 22px rgba(255,111,0,.35); }
        .push-big-btn:disabled { opacity:.55; cursor:wait; }
        .push-status { margin-top:14px; padding:12px 14px; border-radius:10px; font-size:13.5px; font-weight:600; display:none; }
        .push-status.ok { background:#e8f5e9; color:#2e7d32; display:block; }
        .push-status.err { background:#ffebee; color:#c62828; display:block; }
        .push-info { background:#f8f9fa; border-radius:10px; padding:12px 14px; font-size:12.5px; color:#666; line-height:2; margin-top:16px; }
        .push-test { margin-top:14px; width:100%; padding:12px; background:#f5f6f8; border:1px solid #dde1e6; color:#555; border-radius:10px; font-weight:700; cursor:pointer; font-family:inherit; font-size:14px; }
        .push-test:hover { border-color:var(--rang-asli,#FF6F00); color:var(--rang-asli,#FF6F00); }
    </style>
    <h3><i class="fa-solid fa-bell" style="color:var(--rang-asli,#FF6F00)"></i> اعلان گوشی (Web Push)</h3>
    <p style="color:#888;">با فعالسازی، هر پیام چت / فرم تماس / سفارش جدید، مستقیم نوتیف روی این دستگاه میآید — حتی وقتی هیچ پنجرهای باز نیست.</p>

    <div class="push-card">
        <button type="button" class="push-big-btn" id="pbEnable" onclick="pushEnable()">
            <i class="fa-solid fa-mobile-screen-button"></i> فعالسازی اعلان روی این دستگاه
        </button>
        <div class="push-status" id="pbStatus"></div>
        <button type="button" class="push-test" onclick="pushTest()"><i class="fa-solid fa-paper-plane"></i> ارسال اعلان تست</button>
        <div class="push-info">
            <b>وضعیت:</b> <?= $count ?> دستگاه ثبت شده
            <?php if ($keys): ?> | کلید VAPID: <span style="color:#2e7d32;">آماده ✓</span><?php endif; ?>
            <br>اندروید: کروم/فایرفاکس — بعد از زدن دکمه، پیام «اجازه دادن اعلان» را تأیید کن.
        </div>
    </div>

    <script>
    var VAPID_PUB = '<?= htmlspecialchars($keys['pub_b64'] ?? '') ?>';

    function urlB64ToU8(b64) {
        var pad = '='.repeat((4 - b64.length % 4) % 4);
        var base64 = (b64 + pad).replace(/-/g, '+').replace(/_/g, '/');
        var raw = atob(base64);
        var arr = new Uint8Array(raw.length);
        for (var i = 0; i < raw.length; i++) arr[i] = raw.charCodeAt(i);
        return arr;
    }
    function setStatus(cls, txt) { var el = document.getElementById('pbStatus'); el.className = 'push-status ' + cls; el.textContent = txt; }

    async function pushEnable() {
        var btn = document.getElementById('pbEnable');
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) { setStatus('err', 'مرورگر این دستگاه از اعلان پشتیبانی نمیکند.'); return; }
        btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> در حال فعالسازی…';
        try {
            var reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
            await navigator.serviceWorker.ready;
            var perm = await Notification.requestPermission();
            if (perm !== 'granted') { setStatus('err', 'اجازه اعلان داده نشد — از تنظیمات مرورگر برای این سایت اعلان را فعال کن.'); btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-mobile-screen-button"></i> فعالسازی اعلان روی این دستگاه'; return; }
            var sub = await reg.pushManager.getSubscription();
            if (!sub) {
                sub = await reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: urlB64ToU8(VAPID_PUB) });
            }
            var j = sub.toJSON();
            var fd = new FormData();
            fd.append('endpoint', sub.endpoint);
            fd.append('p256dh', j.keys.p256dh);
            fd.append('auth', j.keys.auth);
            fd.append('label', navigator.platform || '');
            var r = await fetch('<?= BASE_URL ?>push-action.php?action=subscribe', { method: 'POST', body: fd });
            var res = await r.json();
            if (res.success) { setStatus('ok', '✓ فعال شد! حالا «ارسال اعلان تست» را بزن تا مطمئن شوی.'); }
            else setStatus('err', res.message || 'خطا در ذخیره');
        } catch (e) {
            setStatus('err', 'خطا: ' + e.message);
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-mobile-screen-button"></i> فعالسازی اعلان روی این دستگاه';
    }

    async function pushTest() {
        setStatus('ok', 'در حال ارسال تست…');
        var fd = new FormData();
        var r = await fetch('<?= BASE_URL ?>push-action.php?action=test', { method: 'POST', body: fd });
        var res = await r.json();
        if (res.sent > 0) setStatus('ok', '✓ ارسال شد به ' + res.sent + ' دستگاه — اعلان را روی گوشی ببین.');
        else setStatus('err', 'ارسال نشد (' + (res.error || 'هیچ دستگاهی ثبت نشده') + ')');
    }
    </script>
    <?php
    include __DIR__ . '/../../ghaleb/ghmod/panevis.php';
}

function push_handle_subscribe() {
    header('Content-Type: application/json; charset=utf-8');
    $endpoint = trim($_POST['endpoint'] ?? '');
    $p256dh = trim($_POST['p256dh'] ?? '');
    $auth = trim($_POST['auth'] ?? '');
    $label = trim($_POST['label'] ?? '');
    if (!$endpoint || !$p256dh || !$auth) {
        echo json_encode(['success' => false, 'message' => 'اطلاعات ناقص']);
        exit;
    }
    $push = new kanal_push();
    $id = $push->save_subscription($endpoint, $p256dh, $auth, substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250), $label);
    echo json_encode(['success' => true, 'id' => $id]);
    exit;
}

function push_handle_test() {
    header('Content-Type: application/json; charset=utf-8');
    $push = new kanal_push();
    $r = $push->sendAll('🔔 اعلان تست مهراد سام', 'اگر این اعلان را میبینی، اعلان گوشی فعال است ✓', '/mod/push');
    echo json_encode($r);
    exit;
}
