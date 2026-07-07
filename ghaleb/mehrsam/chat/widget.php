<style>
.chat-btn {
    position: fixed;
    bottom: 24px;
    left: 24px;
    z-index: 9999;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: var(--rang-makm2);
    color: #fff;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(0,184,148,0.4);
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}
.chat-btn:hover { transform: scale(1.1); }
.chat-btn .badge {
    position: absolute;
    top: -4px;
    right: -4px;
    width: 20px;
    height: 20px;
    background: #e74c3c;
    border-radius: 50%;
    font-size: 11px;
    font-weight: 700;
    display: none;
    align-items: center;
    justify-content: center;
}
.chat-box {
    position: fixed;
    bottom: 90px;
    left: 24px;
    z-index: 9998;
    width: 340px;
    height: 480px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
    direction: rtl;
    font-family: 'Vazirmatn', Tahoma, sans-serif;
}
.chat-box.baz { display: flex; }
.chat-header {
    background: var(--rang-makm2);
    color: #fff;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.chat-header .avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.chat-header .info { flex: 1; }
.chat-header .info strong { font-size: 14px; display: block; }
.chat-header .info small { font-size: 11px; opacity: 0.85; }
.chat-close {
    background: none;
    border: none;
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    opacity: 0.8;
}
.chat-close:hover { opacity: 1; }
.chat-start {
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex: 1;
    justify-content: center;
}
.chat-start input {
    padding: 12px 14px;
    border: 1.5px solid #e0e0e0;
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    outline: none;
    transition: border 0.2s;
}
.chat-start input:focus { border-color: var(--rang-makm2); }
.chat-start button {
    padding: 12px;
    background: var(--rang-makm2);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
}
.chat-start button:hover { background: #00a381; }
.chat-start .error {
    color: var(--rang-makm5);
    font-size: 13px;
    text-align: center;
    display: none;
}
.chat-messages {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 8px;
    background: #f8f9fa;
}
.chat-msg {
    max-width: 80%;
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.6;
    word-wrap: break-word;
}
.chat-msg.user {
    align-self: flex-start;
    background: #e9ecef;
    color: #1a1a1a;
    border-bottom-right-radius: 4px;
}
.chat-msg.admin {
    align-self: flex-end;
    background: var(--rang-makm2);
    color: #fff;
    border-bottom-left-radius: 4px;
}
.chat-msg .time {
    font-size: 10px;
    opacity: 0.6;
    margin-top: 4px;
    display: block;
}
.chat-input-area {
    padding: 12px 16px;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 8px;
    background: #fff;
}
.chat-input-area input {
    flex: 1;
    padding: 10px 14px;
    border: 1.5px solid #e0e0e0;
    border-radius: 8px;
    font-family: inherit;
    font-size: 14px;
    outline: none;
}
.chat-input-area input:focus { border-color: var(--rang-makm2); }
.chat-input-area button {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--rang-makm2);
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    flex-shrink: 0;
}
.chat-input-area button:hover { background: #00a381; }
@media (max-width: 480px) {
    .chat-box { width: calc(100% - 32px); height: 60vh; left: 16px; bottom: 80px; }
}
</style>

<button class="chat-btn" id="chatBtn" onclick="chatToggle()">
    <i class="fa-solid fa-comment-dots"></i>
    <span class="badge" id="chatBadge">1</span>
</button>

<div class="chat-box" id="chatBox">
    <div class="chat-header">
        <div class="avatar"><i class="fa-solid fa-headset"></i></div>
        <div class="info">
            <strong>پشتیبانی <?= SITE_NAME ?></strong>
            <small>پاسخگویی سریع</small>
        </div>
        <button class="chat-close" onclick="chatToggle()"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="chat-start" id="chatStart">
        <p style="text-align:center;color:#666;font-size:13px;margin-bottom:4px;">برای شروع چت، اطلاعات خود را وارد کنید</p>
        <input type="text" id="chatName" placeholder="نام *" autocomplete="name">
        <input type="text" id="chatPhone" placeholder="شماره تلفن *" autocomplete="tel">
        <input type="email" id="chatEmail" placeholder="ایمیل (اختیاری)" autocomplete="email">
        <div class="error" id="chatError"></div>
        <button onclick="chatStart()"><i class="fa-solid fa-play"></i> شروع چت</button>
    </div>

    <div class="chat-messages" id="chatMessages" style="display:none;"></div>

    <div class="chat-input-area" id="chatInputArea" style="display:none;">
        <button onclick="chatSend()"><i class="fa-solid fa-paper-plane"></i></button>
        <input type="text" id="chatInput" placeholder="پیام خود را بنویسید..." onkeydown="if(event.key==='Enter')chatSend()">
    </div>
</div>

<script>
let chatToken = localStorage.getItem('chat_token') || '';
let lastMsgId = 0;
let pollTimer = null;

function chatToggle() {
    const box = document.getElementById('chatBox');
    box.classList.toggle('baz');
    if (box.classList.contains('baz') && chatToken) {
        chatLoadHistory();
        chatStartPolling();
    } else if (!box.classList.contains('baz')) {
        chatStopPolling();
    }
}

function chatStart() {
    const name = document.getElementById('chatName').value.trim();
    const phone = document.getElementById('chatPhone').value.trim();
    const email = document.getElementById('chatEmail').value.trim();
    const error = document.getElementById('chatError');

    if (!name || !phone) {
        error.textContent = 'نام و شماره تلفن الزامی است.';
        error.style.display = 'block';
        return;
    }
    error.style.display = 'none';

    const fd = new FormData();
    fd.append('name', name);
    fd.append('phone', phone);
    fd.append('email', email);

    fetch('<?= BASE_URL ?>chat/start', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                error.textContent = data.error;
                error.style.display = 'block';
                return;
            }
            chatToken = data.token;
            localStorage.setItem('chat_token', chatToken);
            document.getElementById('chatStart').style.display = 'none';
            document.getElementById('chatMessages').style.display = 'flex';
            document.getElementById('chatInputArea').style.display = 'flex';
            chatLoadHistory();
            chatStartPolling();
        })
        .catch(() => {
            error.textContent = 'خطا در برقراری ارتباط.';
            error.style.display = 'block';
        });
}

function chatLoadHistory() {
    if (!chatToken) return;
    fetch('<?= BASE_URL ?>chat/poll?token=' + encodeURIComponent(chatToken) + '&since=0')
        .then(r => r.json())
        .then(data => {
            data.messages.forEach(m => chatAppendMessage(m, true));
            if (data.messages.length) {
                lastMsgId = data.messages[data.messages.length - 1].id;
            }
        });
}

function chatSend() {
    const input = document.getElementById('chatInput');
    const msg = input.value.trim();
    if (!msg) return;

    const fd = new FormData();
    fd.append('token', chatToken);
    fd.append('message', msg);

    fetch('<?= BASE_URL ?>chat/send', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (!data.error) {
                chatAppendMessage({ id: data.id, sender_type: 'user', message: msg, created_at: new Date().toISOString() }, false);
                lastMsgId = data.id;
                input.value = '';
                chatScrollDown();
            }
        });
}

function chatPoll() {
    if (!chatToken) return;
    fetch('<?= BASE_URL ?>chat/poll?token=' + encodeURIComponent(chatToken) + '&since=' + lastMsgId)
        .then(r => r.json())
        .then(data => {
            data.messages.forEach(m => chatAppendMessage(m, false));
            if (data.messages.length) {
                lastMsgId = data.messages[data.messages.length - 1].id;
                chatScrollDown();
            }
            if (data.session_status === 'closed') {
                chatStopPolling();
            }
        });
}

function chatStartPolling() {
    chatStopPolling();
    pollTimer = setInterval(chatPoll, 3000);
}

function chatStopPolling() {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
}

function chatAppendMessage(msg, isHistory) {
    const container = document.getElementById('chatMessages');
    const existing = container.querySelector(`[data-id="${msg.id}"]`);
    if (existing) return;

    const div = document.createElement('div');
    div.className = 'chat-msg ' + msg.sender_type;
    div.dataset.id = msg.id;
    div.textContent = msg.message;

    if (!isHistory) {
        const time = document.createElement('span');
        time.className = 'time';
        time.textContent = new Date().toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit' });
        div.appendChild(time);
    }

    container.appendChild(div);
    if (!isHistory) chatScrollDown();
}

function chatScrollDown() {
    const container = document.getElementById('chatMessages');
    container.scrollTop = container.scrollHeight;
}

if (chatToken) {
    document.getElementById('chatStart').style.display = 'none';
    document.getElementById('chatMessages').style.display = 'flex';
    document.getElementById('chatInputArea').style.display = 'flex';
}
</script>
