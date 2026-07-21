<?php

require_once MASIR_DADE . 'bank.php';

function chat_start_session($name, $phone, $email = '') {
    $token = bin2hex(random_bytes(32));
    $user_id = $_SESSION['user_id'] ?? null;
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("INSERT INTO chat_sessions (user_id, session_token, user_name, user_phone, user_email) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $token, $name, $phone, $email);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    return ['id' => $id, 'token' => $token];
}

function chat_get_session_by_token($token) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM chat_sessions WHERE session_token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $session = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $session;
}

function chat_get_session($id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM chat_sessions WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $session = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $session;
}

function chat_send_message($session_id, $sender_type, $message) {
    $bank = new Bank();
    $conn = $bank->getConnection();

    $stmt = $conn->prepare("SELECT user_name FROM chat_sessions WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $user_name = $stmt->get_result()->fetch_assoc()['user_name'] ?? '';
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO chat_messages (session_id, sender_type, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $session_id, $sender_type, $message);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    $conn->query("UPDATE chat_sessions SET last_activity = NOW(), status = 'active' WHERE id = $session_id");
    $conn->close();

    if ($sender_type === 'user') {
        @require_once MASIR_RISH . 'afzuneh/elpayaagh/Notifier.php';
        if (class_exists('Notifier')) {
            Notifier::notify("💬 <b>پیام جدید در چت</b>\nکاربر: {$user_name}\nپیام: " . mb_substr($message, 0, 100));
        }
    }

    return $id;
}

function chat_get_messages($session_id, $since_id = 0) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM chat_messages WHERE session_id = ? AND id > ? ORDER BY id ASC");
    $stmt->bind_param("ii", $session_id, $since_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];
    while ($row = $result->fetch_assoc()) $messages[] = $row;
    $stmt->close();
    $conn->close();
    return $messages;
}

function chat_get_all_messages($session_id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("SELECT * FROM chat_messages WHERE session_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];
    while ($row = $result->fetch_assoc()) $messages[] = $row;
    $stmt->close();
    $conn->close();
    return $messages;
}

function chat_get_active_sessions() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $result = $conn->query("SELECT cs.*, (SELECT COUNT(*) FROM chat_messages cm WHERE cm.session_id = cs.id AND cm.sender_type = 'user') AS unread FROM chat_sessions cs WHERE cs.status IN ('waiting','active') ORDER BY cs.last_activity DESC");
    $sessions = [];
    while ($row = $result->fetch_assoc()) $sessions[] = $row;
    $conn->close();
    return $sessions;
}

function chat_get_closed_sessions() {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $result = $conn->query("SELECT cs.* FROM chat_sessions cs WHERE cs.status = 'closed' ORDER BY cs.last_activity DESC LIMIT 50");
    $sessions = [];
    while ($row = $result->fetch_assoc()) $sessions[] = $row;
    $conn->close();
    return $sessions;
}

function chat_close_session($id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("UPDATE chat_sessions SET status = 'closed' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function chat_delete_session($id) {
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("DELETE FROM chat_messages WHERE session_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $stmt = $conn->prepare("DELETE FROM chat_sessions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function chat_cleanup_old() {
    $cutoff = date('Y-m-d H:i:s', strtotime('-6 months'));
    $bank = new Bank();
    $conn = $bank->getConnection();
    $stmt = $conn->prepare("DELETE cm FROM chat_messages cm JOIN chat_sessions cs ON cm.session_id = cs.id WHERE cs.created_at < ?");
    $stmt->bind_param("s", $cutoff);
    $stmt->execute();
    $stmt->close();
    $conn->query("DELETE FROM chat_sessions WHERE created_at < '$cutoff'");
    $conn->close();
}
