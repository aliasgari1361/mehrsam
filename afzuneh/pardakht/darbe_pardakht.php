<?php
/**
 * کلاس پایه برای درگاه‌های پرداخت
 * هر درگاه باید متدهای request و verify را پیاده‌سازی کند
 */
abstract class darbe_pardakht {
    protected $settings = [];
    protected $sandbox = true;
    protected $callback_url = '';

    public function __construct($settings = []) {
        $this->settings = $settings;
        $this->callback_url = BASE_URL . '/forushgah/checkout/' . $this->getKey() . '/callback';
    }

    /** نام کلید درگاه (مثل zarinpal) */
    abstract public function getKey(): string;

    /** عنوان نمایشی درگاه */
    abstract public function getTitle(): string;

    /** ارسال درخواست پرداخت - برگشت: ['success'=>bool, 'redirect_url'=>string, 'authority'=>string, 'message'=>string] */
    abstract public function request($amount, $order_id, $description = ''): array;

    /** تایید پرداخت - برگشت: ['success'=>bool, 'ref_id'=>string, 'message'=>string] */
    abstract public function verify($amount, $authority): array;

    /** آیا درگاه فعال است؟ */
    public function isEnabled(): bool {
        return !empty($this->settings['enabled']);
    }

    /** ارسال درخواست cURL */
    protected function curlPost($url, $data, $headers = []) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(['Content-Type: application/json'], $headers));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['body' => json_decode($response, true), 'code' => $http_code];
    }
}
