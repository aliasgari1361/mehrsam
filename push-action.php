<?php
/**
 * endpoint عمومی Web Push — بدون نیاز به لاگین
 * subscribe (POST) + test (POST)
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/haste/tanzimat.php';
require_once __DIR__ . '/dade/bank.php';
require_once __DIR__ . '/afzuneh/elpayaagh/channels/kanal_push.php';

$action = trim($_POST['action'] ?? $_GET['action'] ?? '');

switch ($action) {
    case 'subscribe':
        $endpoint = trim($_POST['endpoint'] ?? '');
        $p256dh   = trim($_POST['p256dh'] ?? '');
        $auth     = trim($_POST['auth'] ?? '');
        $label    = trim($_POST['label'] ?? '');

        if (!$endpoint || !$p256dh || !$auth) {
            echo json_encode(['success' => false, 'message' => 'اطلاعات ناقص']);
            exit;
        }

        $push = new kanal_push();
        $id = $push->save_subscription(
            $endpoint,
            $p256dh,
            $auth,
            substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250),
            $label
        );
        echo json_encode(['success' => true, 'id' => $id]);
        exit;

    case 'test':
        $push = new kanal_push();
        $r = $push->sendAll(
            '🔔 اعلان تست مهراد سام',
            'اگر این اعلان را میبینی، اعلان گوشی فعال است ✓',
            '/mod/push'
        );
        echo json_encode($r);
        exit;

    default:
        echo json_encode(['success' => false, 'message' => 'action نامعتبر']);
        exit;
}
