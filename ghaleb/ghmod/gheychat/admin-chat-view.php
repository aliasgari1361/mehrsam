<?php if (!isset($chat_session)) return; ?>
<?php $last_msg_id = !empty($chat_messages) ? end($chat_messages)['id'] : 0; ?>
<h3>چت با <?= htmlspecialchars($chat_session['user_name']) ?></h3>
<p style="color:#888;margin-bottom:20px;">تلفن: <?= htmlspecialchars($chat_session['user_phone']) ?></p>

<div id="chat-msg-container" style="background:#f8f9fa;border-radius:12px;padding:20px;margin-bottom:20px;max-height:400px;overflow-y:auto;" data-last-id="<?= $last_msg_id ?>">
    <?php if (!empty($chat_messages)): ?>
    <?php foreach ($chat_messages as $m): ?>
    <div class="chat-msg" data-id="<?= $m['id'] ?>">
        <div style="margin-bottom:12px;display:flex;flex-direction:column;align-items:<?= $m['sender_type'] === 'admin' ? 'flex-start' : 'flex-end' ?>;">
            <div style="background:<?= $m['sender_type'] === 'admin' ? '#00B894' : '#e9ecef' ?>;color:<?= $m['sender_type'] === 'admin' ? '#fff' : '#1a1a1a' ?>;padding:10px 16px;border-radius:12px;max-width:70%;font-size:14px;<?= $m['sender_type'] === 'admin' ? '' : 'border-bottom-left-radius:4px' ?>;">
                <?= htmlspecialchars($m['message']) ?>
            </div>
            <small style="font-size:11px;color:#aaa;margin-top:4px;"><?= $m['created_at'] ?></small>
        </div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <p id="chat-empty-msg">پیامی وجود ندارد.</p>
    <?php endif; ?>
</div>

<?php if ($chat_session['status'] !== 'closed'): ?>
<form method="post" action="<?= BASE_URL ?>mod/chat_reply/<?= $chat_session['id'] ?>" style="display:flex;gap:8px;">
    <input type="text" name="message" placeholder="پاسخ خود را بنویسید..." required style="flex:1;padding:10px 14px;border:1.5px solid #e0e0e0;border-radius:8px;font-family:inherit;font-size:14px;">
    <button type="submit" class="dakmeh dakmeh-asli" style="padding:10px 20px;">ارسال</button>
</form>

<form method="post" action="<?= BASE_URL ?>mod/chat_close/<?= $chat_session['id'] ?>" style="margin-top:10px;">
    <button type="submit" class="dakmeh" style="background:#e17055;color:#fff;border:none;padding:8px 16px;border-radius:8px;cursor:pointer;" onclick="return confirm('بستن چت؟')">بستن چت</button>
</form>
<?php else: ?>
<p style="color:#888;">این چت بسته شده است.</p>
<?php endif; ?>

<div style="margin-top:16px;display:flex;gap:8px;">
    <a href="<?= BASE_URL ?>mod/chat" class="dakmeh dakmeh-khali" style="padding:8px 16px;font-size:13px;">← بازگشت به لیست</a>
    <a href="<?= BASE_URL ?>mod/chat_delete/<?= $chat_session['id'] ?>" class="dakmeh" style="background:#c62828;color:#fff;padding:8px 16px;font-size:13px;border:none;border-radius:8px;cursor:pointer;" onclick="return confirm('کل این چت برای همیشه حذف شود؟')"><i class="fa-solid fa-trash"></i> حذف چت</a>
</div>

<script>
(function(){
    var container = document.getElementById('chat-msg-container');
    if (!container) return;
    var sessionId = <?= $chat_session['id'] ?>;
    var lastId = parseInt(container.getAttribute('data-last-id') || '0');
    var soundCtx = null;

    function playBeep() {
        try {
            if (!soundCtx) soundCtx = new (window.AudioContext || window.webkitAudioContext)();
            var osc = soundCtx.createOscillator();
            var gain = soundCtx.createGain();
            osc.connect(gain);
            gain.connect(soundCtx.destination);
            osc.frequency.value = 800;
            osc.type = 'sine';
            gain.gain.setValueAtTime(0.3, soundCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, soundCtx.currentTime + 0.4);
            osc.start(soundCtx.currentTime);
            osc.stop(soundCtx.currentTime + 0.4);
        } catch(e) {}
    }

    function showDesktopNotification(title, body) {
        if (!('Notification' in window) || Notification.permission === 'denied') return;
        if (Notification.permission === 'granted') {
            new Notification(title, { body: body, icon: '' });
        } else {
            Notification.requestPermission();
        }
    }

    function poll() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '<?= BASE_URL ?>mod/chat_poll_admin/' + sessionId + '?since=' + lastId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.messages && data.messages.length > 0) {
                        var emptyMsg = document.getElementById('chat-empty-msg');
                        if (emptyMsg) emptyMsg.remove();

                        data.messages.forEach(function(msg) {
                            if (msg.id <= lastId) return;
                            lastId = msg.id;

                            var div = document.createElement('div');
                            div.className = 'chat-msg';
                            div.setAttribute('data-id', msg.id);
                            var align = msg.sender_type === 'admin' ? 'flex-start' : 'flex-end';
                            var bg = msg.sender_type === 'admin' ? '#00B894' : '#e9ecef';
                            var color = msg.sender_type === 'admin' ? '#fff' : '#1a1a1a';
                            div.innerHTML = '<div style="margin-bottom:12px;display:flex;flex-direction:column;align-items:' + align + ';">' +
                                '<div style="background:' + bg + ';color:' + color + ';padding:10px 16px;border-radius:12px;max-width:70%;font-size:14px;">' +
                                escapeHtml(msg.message) + '</div>' +
                                '<small style="font-size:11px;color:#aaa;margin-top:4px;">' + escapeHtml(msg.created_at) + '</small></div>';
                            container.appendChild(div);

                            if (msg.sender_type === 'user') {
                                playBeep();
                                showDesktopNotification('پیام جدید از ' + '<?= htmlspecialchars($chat_session['user_name']) ?>', msg.message);
                            }
                        });
                        container.scrollTop = container.scrollHeight;
                    }
                } catch(e) {}
            }
            setTimeout(poll, 5000);
        };
        xhr.onerror = function() { setTimeout(poll, 5000); };
        xhr.send();
    }

    function escapeHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    if (Notification && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    container.scrollTop = container.scrollHeight;
    setTimeout(poll, 3000);
})();
</script>
