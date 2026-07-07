<?php

require_once MASIR_DADE . 'bank.php';
require_once __DIR__ . '/chat-model.php';

function chat_route($action, $params) {
    switch ($action) {
        case 'start':
            chat_handle_start();
            break;
        case 'send':
            chat_handle_send();
            break;
        case 'poll':
            chat_handle_poll($params[0] ?? 0);
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'not found']);
            break;
    }
}

function chat_handle_start() {
    header('Content-Type: application/json');
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($phone)) {
        http_response_code(400);
        echo json_encode(['error' => 'نام و شماره تلفن الزامی است.']);
        return;
    }

    $session = chat_start_session($name, $phone, $email);
    $_SESSION['chat_token'] = $session['token'];
    chat_send_message($session['id'], 'user', 'سلام، چطور میتونم کمک کنم؟');
    echo json_encode(['id' => $session['id'], 'token' => $session['token']]);
}

function chat_handle_send() {
    header('Content-Type: application/json');
    $token = $_POST['token'] ?? $_SESSION['chat_token'] ?? '';
    $message = trim($_POST['message'] ?? '');

    if (empty($token) || empty($message)) {
        http_response_code(400);
        echo json_encode(['error' => 'اطلاعات ناقص']);
        return;
    }

    $session = chat_get_session_by_token($token);
    if (!$session || $session['status'] === 'closed') {
        http_response_code(400);
        echo json_encode(['error' => 'چت بسته شده است.']);
        return;
    }

    $id = chat_send_message($session['id'], 'user', $message);
    echo json_encode(['id' => $id]);
}

function chat_handle_poll($since_id = 0) {
    header('Content-Type: application/json');

    if (rand(1, 200) === 1) {
        chat_cleanup_old();
    }

    $token = $_GET['token'] ?? $_SESSION['chat_token'] ?? '';

    if (empty($token)) {
        echo json_encode(['messages' => []]);
        return;
    }

    $session = chat_get_session_by_token($token);
    if (!$session) {
        echo json_encode(['messages' => []]);
        return;
    }

    $messages = chat_get_messages($session['id'], (int)$since_id);
    echo json_encode([
        'session_status' => $session['status'],
        'messages' => $messages
    ]);
}
