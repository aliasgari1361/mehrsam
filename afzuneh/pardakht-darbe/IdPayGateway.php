<?php
/**
 * درگاه پرداخت آی‌دی‌پی (IDPay)
 */
class IdPayGateway extends PaymentGateway {
    private $api_key;

    public function __construct($settings = []) {
        parent::__construct($settings);
        $this->api_key = $settings['api_key'] ?? '';
        $this->sandbox = !empty($settings['sandbox']);
    }

    public function getKey(): string { return 'idpay'; }
    public function getTitle(): string { return 'آی‌دی‌پی'; }

    public function request($amount, $order_id, $description = ''): array {
        if (empty($this->api_key)) {
            return ['success' => false, 'message' => 'API Key آی‌دی‌پی تنظیم نشده است'];
        }

        $api_url = $this->sandbox
            ? 'https://sandbox.idpay.ir/payment'
            : 'https://api.idpay.ir/v1.1/payment';
        $verify_url = $this->sandbox
            ? 'https://sandbox.idpay.ir/payment/verify'
            : 'https://api.idpay.ir/v1.1/payment/verify';

        $data = [
            'order_id' => $order_id,
            'amount' => (int)$amount,
            'callback' => $this->callback_url,
            'desc' => $description . ' - سفارش #' . $order_id,
            'name' => $_SESSION['user_name'] ?? '',
            'mail' => $_SESSION['user_email'] ?? '',
            'phone' => $_SESSION['user_mobile'] ?? '',
        ];

        $result = $this->curlPost($api_url, $data, [
            'X-API-KEY: ' . $this->api_key,
            'X-SANDBOX: ' . ($this->sandbox ? '1' : '0'),
        ]);

        $body = $result['body'];
        if ($result['code'] === 201 && !empty($body['id']) && !empty($body['link'])) {
            return [
                'success' => true,
                'authority' => $body['id'],
                'redirect_url' => $body['link']
            ];
        }

        $message = $body['error_message'] ?? ($body['errors'][0]['message'] ?? 'خطای ناشناخته در اتصال به آی‌دی‌پی');
        return ['success' => false, 'message' => $message];
    }

    public function verify($amount, $authority): array {
        if (empty($this->api_key)) {
            return ['success' => false, 'message' => 'API Key آی‌دی‌پی تنظیم نشده است'];
        }

        $verify_url = $this->sandbox
            ? 'https://sandbox.idpay.ir/payment/verify'
            : 'https://api.idpay.ir/v1.1/payment/verify';

        $data = [
            'id' => $authority,
            'order_id' => $_POST['order_id'] ?? '',
        ];

        $result = $this->curlPost($verify_url, $data, [
            'X-API-KEY: ' . $this->api_key,
            'X-SANDBOX: ' . ($this->sandbox ? '1' : '0'),
        ]);

        $body = $result['body'];
        if ($result['code'] === 200 && isset($body['status']) && in_array($body['status'], [100, 101])) {
            return [
                'success' => true,
                'ref_id' => $body['track_id'] ?? $authority,
                'message' => 'پرداخت با موفقیت تایید شد'
            ];
        }

        $message = $body['error_message'] ?? ($body['errors'][0]['message'] ?? 'خطای ناشناخته در تایید آی‌دی‌پی');
        return ['success' => false, 'message' => $message];
    }
}
