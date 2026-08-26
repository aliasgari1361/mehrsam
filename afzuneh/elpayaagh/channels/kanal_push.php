<?php

require_once __DIR__ . '/../webpush.php';
if (!class_exists('Bank')) { require_once MASIR_DADE . 'bank.php'; }

class kanal_push {

    /* ---------- دسترسی به تنظیمات (site_settings.json → push.*) ---------- */
    private function cfg() {
        global $site_settings;
        $s = is_array($site_settings) ? ($site_settings['push'] ?? []) : [];
        if (empty($s['vapid_pub']) || empty($s['vapid_priv'])) {
            /* تولید خودکار کلیدهای VAPID در اولین استفاده */
            $res = WebPush_Engine::new_ec_key();
            if (!$res) return null;
            $priv = WebPush_Engine::ec_export_priv_pem($res);
            $pub = WebPush_Engine::ec_pub_raw($res);
            if (!$priv || !$pub) return null;
            $s = ['vapid_priv' => $priv, 'vapid_pub' => WebPush_Engine::b64e($pub)];
            if (function_exists('save_site_settings')) {
                save_site_settings(['push' => $s]);
                global $site_settings;
                $site_settings['push'] = $s;
            }
        }
        return ['priv_pem' => $s['vapid_priv'], 'pub_raw' => WebPush_Engine::b64d($s['vapid_pub']), 'pub_b64' => $s['vapid_pub']];
    }

    private function all_subscriptions() {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $res = $conn->query("SELECT id, endpoint, p256dh, auth FROM push_subscriptions");
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $conn->close();
        return $rows;
    }

    private function delete_subscription($id) {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $stmt = $conn->prepare("DELETE FROM push_subscriptions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    public function count_subscriptions() {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $res = @$conn->query("SELECT COUNT(*) AS c FROM push_subscriptions");
        $n = $res ? (int)($res->fetch_assoc()['c'] ?? 0) : 0;
        $conn->close();
        return $n;
    }

    /**
     * ارسال اعلان
     * @param array|null $to رکورد اشتراک خاص یا null = همه گوشیها
     * @param string $title عنوان
     * @param string $body متن
     * @param string $url لینک کلیک روی اعلان
     * @return array ['ok'=>bool,'sent'=>n,'dead'=>n,'total'=>n]
     */
    public function send($to, $title, $body, $url = '/mod/chat') {
        $keys = $this->cfg();
        if (!$keys) return ['ok' => false, 'sent' => 0, 'dead' => 0, 'total' => 0, 'error' => 'vapid'];

        if (!@$this->table_exists()) return ['ok' => false, 'sent' => 0, 'dead' => 0, 'total' => 0, 'error' => 'table'];

        $rows = $to ? [$to] : $this->all_subscriptions();
        $sent = 0; $dead = 0;
        foreach ($rows as $s) {
            $payload = json_encode([
                'title' => $title,
                'body' => mb_substr((string)$body, 0, 300),
                'url' => $url,
                'icon' => (defined('BASE_URL') ? BASE_URL : '/') . 'ghaleb/manabe/favicon.png',
            ], JSON_UNESCAPED_UNICODE);
            $r = WebPush_Engine::send($s['endpoint'], $s['p256dh'], $s['auth'], $payload, $keys['priv_pem'], $keys['pub_raw']);
            if ($r['ok']) { $sent++; }
            elseif (in_array($r['status'], [404, 410], true)) { $this->delete_subscription($s['id']); $dead++; }
        }
        return ['ok' => $sent > 0, 'sent' => $sent, 'dead' => $dead, 'total' => count($rows)];
    }

    public function sendAll($title, $body, $url = '/mod/chat') {
        return $this->send(null, $title, $body, $url);
    }

    public function notifyAdmin($title, $body, $url = '/mod/chat') {
        return $this->send(null, $title, $body, $url);
    }

    /* ---------- ذخیره اشتراک جدید (upsert بر اساس endpoint) ---------- */
    public function save_subscription($endpoint, $p256dh, $auth, $user_agent = '', $label = '') {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $stmt = $conn->prepare("SELECT id FROM push_subscriptions WHERE endpoint = ? LIMIT 1");
        $stmt->bind_param("s", $endpoint);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row) {
            $stmt = $conn->prepare("UPDATE push_subscriptions SET p256dh = ?, auth = ?, user_agent = ? WHERE id = ?");
            $stmt->bind_param("sssi", $p256dh, $auth, $user_agent, $row['id']);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            return (int)$row['id'];
        }
        $stmt = $conn->prepare("INSERT INTO push_subscriptions (endpoint, p256dh, auth, user_agent, label) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssss", $endpoint, $p256dh, $auth, $user_agent, $label);
        $stmt->execute();
        $new_id = $conn->insert_id;
        $stmt->close();
        $conn->close();
        return (int)$new_id;
    }

    private function table_exists() {
        $bank = new Bank();
        $conn = $bank->getConnection();
        $res = $conn->query("SHOW TABLES LIKE 'push_subscriptions'");
        $ok = $res && $res->num_rows > 0;
        $conn->close();
        return $ok;
    }
}
