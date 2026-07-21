<?php
/**
 * درگاه پرداخت زیبال (Zibal)
 */
class darbe_zibal extends darbe_pardakht {
    private $merchant;

    public function __construct($settings = []) {
        parent::__construct($settings);
        $this->merchant = $settings['merchant'] ?? '';
        $this->sandbox = !empty($settings['sandbox']);
    }

    public function getKey(): string { return 'zibal'; }
    public function getTitle(): string { return 'زیبال'; }

    public function request($amount, $order_id, $description = ''): array {
        if (empty($this->merchant)) {
            return ['success' => false, 'message' => 'مرچنت کد زیبال تنظیم نشده است'];
        }

        $api_url = 'https://gateway.zibal.ir/v1/request';
        $startpay = 'https://gateway.zibal.ir/start/';

        $data = [
            'merchant' => $this->merchant,
            'amount' => (int)$amount,
            'callbackUrl' => $this->callback_url,
            'description' => $description . ' - سفارش #' . $order_id,
            'orderId' => $order_id,
            'mobile' => $_SESSION['user_mobile'] ?? '',
        ];

        $result = $this->curlPost($api_url, $data);

        $body = $result['body'];
        if (isset($body['result']) && $body['result'] == 100 && !empty($body['trackId'])) {
            return [
                'success' => true,
                'authority' => $body['trackId'],
                'redirect_url' => $startpay . $body['trackId']
            ];
        }

        $message = $body['message'] ?? 'خطای ناشناخته در اتصال به زیبال';
        return ['success' => false, 'message' => $message];
    }

    public function verify($amount, $authority): array {
        if (empty($this->merchant)) {
            return ['success' => false, 'message' => 'مرچنت کد زیبال تنظیم نشده است'];
        }

        $verify_url = 'https://gateway.zibal.ir/v1/verify';

        $data = [
            'merchant' => $this->merchant,
            'trackId' => $authority,
        ];

        $result = $this->curlPost($verify_url, $data);

        $body = $result['body'];
        if (isset($body['result']) && in_array($body['result'], [100, 201])) {
            return [
                'success' => true,
                'ref_id' => $body['refNumber'] ?? $authority,
                'message' => 'پرداخت با موفقیت تایید شد'
            ];
        }

        $message = $body['message'] ?? 'خطای ناشناخته در تایید زیبال';
        return ['success' => false, 'message' => $message];
    }
}
