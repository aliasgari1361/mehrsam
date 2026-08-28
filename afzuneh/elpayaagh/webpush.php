<?php
/**
 * موتور Web Push — استاندارد RFC 8291 (aes128gcm) + RFC 8292 (VAPID)
 * فقط با openssl داخلی PHP — بدون هیچ کتابخانه خارجی (قانون ۹.۸)
 */
class WebPush_Engine {

    /* ---------- Base64URL ---------- */
    public static function b64e($bin) { return rtrim(strtr(base64_encode($bin), '+/', '-_'), '='); }
    public static function b64d($str) {
        $str = strtr(trim($str), '-_', '+/');
        $pad = strlen($str) % 4;
        if ($pad) $str .= str_repeat('=', 4 - $pad);
        return base64_decode($str);
    }

    /* ---------- HKDF (RFC 5869, SHA-256) ---------- */
    public static function hkdf($salt, $ikm, $info, $len) {
        $prk = hash_hmac('sha256', $ikm, $salt, true);
        $t = ''; $okm = ''; $i = 1;
        while (strlen($okm) < $len) {
            $t = hash_hmac('sha256', $t . $info . chr($i), $prk, true);
            $okm .= $t; $i++;
        }
        return substr($okm, 0, $len);
    }

    /* ---------- کلیدهای EC P-256 ---------- */
    private static $last_cnf = null;

    private static function cnf_candidates() {
        $list = [];
        $env = getenv('OPENSSL_CONF');
        if ($env) $list[] = $env;
        $list[] = __DIR__ . '/openssl.cnf'; /* همراه افزونه — همهجا موجود */
        if (defined('WINDOWS') || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $list[] = 'D:\MAMP\bin\apache\conf\openssl.cnf';
        }
        return $list;
    }

    public static function new_ec_key() {
        $base = ['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC];
        self::$last_cnf = null;
        /* لینوکس/هاست: بدون config کار میکند */
        $res = @openssl_pkey_new($base);
        if (!$res) {
            /* ویندوز: نیاز به openssl.cnf */
            foreach (self::cnf_candidates() as $cnf) {
                if (!is_file($cnf)) continue;
                $res = @openssl_pkey_new(array_merge($base, ['config' => $cnf]));
                if ($res) { self::$last_cnf = $cnf; break; }
            }
        }
        return $res ?: null;
    }

    private static function ssl_opts() {
        return self::$last_cnf ? ['config' => self::$last_cnf] : null;
    }

    public static function ec_pub_raw($res) {
        $d = openssl_pkey_get_details($res);
        if (!$d || !isset($d['key'])) return null;
        $x = str_pad($d['ec']['x'], 32, "\x00", STR_PAD_LEFT);
        $y = str_pad($d['ec']['y'], 32, "\x00", STR_PAD_LEFT);
        return "\x04" . $x . $y;
    }

    public static function ec_export_priv_pem($res) {
        $out = '';
        if (!openssl_pkey_export($res, $out, null, self::ssl_opts())) return null;
        return $out;
    }

    /* کلید عمومی خام ۶۵ بایتی (0x04||X||Y) → PEM برای openssl */
    public static function raw_pub_to_pem($raw) {
        $der = "\x30\x59\x30\x13\x06\x07\x2a\x86\x48\xce\x3d\x02\x01\x06\x08\x2a\x86\x48\xce\x3d\x03\x01\x07\x03\x42\x00" . $raw;
        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($der), 64, "\n") . "-----END PUBLIC KEY-----\n";
    }

    /* ---------- امضای ES256 (VAPID) ---------- */
    public static function der_sig_to_raw($der) {
        /* DER: 30 len  02 rLen <r>  02 sLen <s> */
        $p = 2;
        $rLen = ord($der[$p + 1]);
        $r = substr($der, $p + 2, $rLen);
        $p += 2 + $rLen;
        $sLen = ord($der[$p + 1]);
        $s = substr($der, $p + 2, $sLen);
        $r = ltrim($r, "\x00");
        $s = ltrim($s, "\x00");
        return str_pad($r, 32, "\x00", STR_PAD_LEFT) . str_pad($s, 32, "\x00", STR_PAD_LEFT);
    }

    public static function vapid_header($endpoint, $priv_pem, $pub_raw) {
        $u = parse_url($endpoint);
        $aud = ($u['scheme'] ?? 'https') . '://' . ($u['host'] ?? '');
        $email = defined('SITE_EMAIL') && SITE_EMAIL ? SITE_EMAIL : 'admin@localhost';
        $header = self::b64e(json_encode(['typ' => 'JWT', 'alg' => 'ES256']));
        $claims = self::b64e(json_encode([
            'aud' => $aud,
            'exp' => time() + 43200,
            'sub' => 'mailto:' . $email,
        ]));
        $input = $header . '.' . $claims;
        openssl_sign($input, $der_sig, $priv_pem, OPENSSL_ALGO_SHA256);
        $jwt = $input . '.' . self::b64e(self::der_sig_to_raw($der_sig));
        return 'vapid t=' . $jwt . ', k=' . self::b64e($pub_raw);
    }

    /* ---------- رمزنگاری payload (aes128gcm, RFC 8291) ---------- */
    public static function encrypt($payload, $p256dh_b64, $auth_b64, &$eph_pub_raw) {
        $ua = self::b64d($p256dh_b64);
        $auth = self::b64d($auth_b64);
        if (strlen($ua) !== 65 || ord($ua[0]) !== 4 || strlen($auth) < 16) return false;

        $eph = self::new_ec_key();
        if (!$eph) return false;
        $eph_pub_raw = self::ec_pub_raw($eph);
        if (!$eph_pub_raw || strlen($eph_pub_raw) !== 65) return false;

        $shared = openssl_pkey_derive(self::raw_pub_to_pem($ua), $eph);
        if ($shared === false) return false;

        // RFC 8291: extract + expand
        $prk_key = self::hkdf($auth, $shared, "WebPush: info\x00" . $ua . $eph_pub_raw, 32);
        $salt = random_bytes(16);
        $cek = self::hkdf($salt, $prk_key, "Content-Encoding: aes128gcm\x00", 16);
        $nonce = self::hkdf($salt, $prk_key, "Content-Encoding: nonce\x00", 12);

        // هدر record aes128gcm (86 بایت): salt + rsid(4096) + کلید دو(87) + کلید عمومی موقت
        $header = $salt . pack('N', 4096) . chr(86 + 1) . $eph_pub_raw;

        $tag = '';
        $ciphertext = openssl_encrypt($payload, 'aes-128-gcm', $cek, OPENSSL_RAW_DATA, $nonce, $tag, $header);
        if ($ciphertext === false) return false;

        return $header . $ciphertext . $tag;
    }

    /* ---------- ارسال کامل به یک اشتراک ---------- */
    /**
     * @return array ['ok'=>bool,'status'=>int|0,'error'=>string]
     */
    public static function send($endpoint, $p256dh_b64, $auth_b64, $payload_json, $priv_pem, $pub_raw) {
        $eph_pub_raw = '';
        $body = self::encrypt($payload_json, $p256dh_b64, $auth_b64, $eph_pub_raw);
        if ($body === false) return ['ok' => false, 'status' => 0, 'error' => 'encrypt failed'];

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/octet-stream',
                'Content-Encoding: aes128gcm',
                'TTL: 2419200',
                'Priority: high',
                'Urgency: high',
                'Authorization: ' . self::vapid_header($endpoint, $priv_pem, $pub_raw),
            ],
        ]);
        $resp = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($resp === false) return ['ok' => false, 'status' => 0, 'error' => $err ?: 'curl failed'];
        /* 200/201 = رسیده؛ 404/410 = منقضی (باید حذف شود) */
        return ['ok' => in_array($status, [200, 201], true), 'status' => $status, 'error' => in_array($status, [200, 201], true) ? '' : 'HTTP ' . $status];
    }
}
