<?php
/**
 * درگاه پرداخت زرین‌پال
 */
class darbe_zarinpal extends darbe_pardakht {
    private $merchant_id;

    public function __construct($settings = []) {
        parent::__construct($settings);
        $this->merchant_id = $settings['merchant'] ?? '';
        $this->sandbox = !empty($settings['sandbox']);
    }

    public function getKey(): string { return 'zarinpal'; }
    public function getTitle(): string { return 'زرین‌پال'; }

    public function request($amount, $order_id, $description = ''): array {
        if (empty($this->merchant_id)) {
            return ['success' => false, 'message' => 'مرچنت کد زرین‌پال تنظیم نشده است'];
        }

        $api_url = $this->sandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment/request.json'
            : 'https://api.zarinpal.com/pg/v4/payment/request.json';
        $startpay = $this->sandbox
            ? 'https://sandbox.zarinpal.com/pg/StartPay/'
            : 'https://www.zarinpal.com/pg/StartPay/';

        $data = [
            'merchant_id' => $this->merchant_id,
            'amount' => (int)$amount,
            'callback_url' => $this->callback_url,
            'description' => $description . ' - سفارش #' . $order_id,
            'metadata' => [
                'order_id' => $order_id,
                'mobile' => $_SESSION['user_mobile'] ?? '',
                'email' => $_SESSION['user_email'] ?? ''
            ]
        ];

        $result = $this->curlPost($api_url, $data);

        if ($result['code'] === 200 && isset($result['body']['data']['code']) && $result['body']['data']['code'] === 100) {
            return [
                'success' => true,
                'authority' => $result['body']['data']['authority'],
                'redirect_url' => $startpay . $result['body']['data']['authority']
            ];
        }

        $code = $result['body']['data']['code'] ?? -1;
        $errors = [
            -9 => 'خطای اعتبارسنجی', -10 => 'ترمینال تایید نشده', -11 => 'مرچنت کد صحیح نیست',
            -12 => 'مرچنت کد فعال نیست', -15 => 'ترمینال معلق شده', -16 => 'سطح تایید پایین',
            -17 => 'مبلغ نادرست', -18 => 'مبلغ کمتر از حداقل', -19 => 'مبلغ بیشتر از حداکثر',
            -20 => 'مبلغ پرداختی با مبلغ درخواستی متفاوت', -21 => 'پرداخت یافت نشد',
            -22 => 'تراکنش ناموفق', -23 => 'خطای درگاه', -30 => 'فرمت JSON نادرست',
            -31 => 'مرچنت کد الزامی', -32 => 'مبلغ الزامی', -33 => 'Callback URL الزامی',
            -34 => 'Description الزامی', -40 => 'متادیتا معتبر نیست', -41 => 'موبایل معتبر نیست',
            -42 => 'ایمیل معتبر نیست', -50 => 'Callback URL معتبر نیست',
            -51 => 'Description طولانی است', -52 => 'موبایل طولانی است',
            -53 => 'ایمیل طولانی است', -54 => 'Description خالی است',
        ];
        $message = $errors[$code] ?? ($result['body']['errors'][0]['message'] ?? 'خطای ناشناخته');
        return ['success' => false, 'message' => $message, 'code' => $code];
    }

    public function verify($amount, $authority): array {
        if (empty($this->merchant_id)) {
            return ['success' => false, 'message' => 'مرچنت کد زرین‌پال تنظیم نشده است'];
        }

        $verify_url = $this->sandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json'
            : 'https://api.zarinpal.com/pg/v4/payment/verify.json';

        $data = [
            'merchant_id' => $this->merchant_id,
            'amount' => (int)$amount,
            'authority' => $authority
        ];

        $result = $this->curlPost($verify_url, $data);

        if ($result['code'] === 200 && isset($result['body']['data']['code'])) {
            if ($result['body']['data']['code'] === 100 || $result['body']['data']['code'] === 101) {
                return [
                    'success' => true,
                    'ref_id' => $result['body']['data']['ref_id'],
                    'message' => $result['body']['data']['code'] === 101 ? 'تراکنش تکراری (قبلاً تایید شده)' : 'پرداخت با موفقیت انجام شد'
                ];
            }
        }

        $code = $result['body']['data']['code'] ?? -1;
        $errors = [
            -9 => 'خطای اعتبارسنجی', -10 => 'ترمینال تایید نشده', -11 => 'مرچنت کد صحیح نیست',
            -12 => 'مرچنت کد فعال نیست', -13 => 'پرداخت یافت نشد', -14 => 'مرچنت کد معتبر نیست',
            -15 => 'ترمینال معلق شده', -16 => 'سطح تایید پایین', -17 => 'مبلغ نادرست',
            -18 => 'مبلغ کمتر از حداقل', -19 => 'مبلغ بیشتر از حداکثر', -20 => 'مبلغ پرداختی با مبلغ درخواستی متفاوت',
            -21 => 'پرداخت یافت نشد', -22 => 'تراکنش ناموفق', -23 => 'خطای درگاه',
            -30 => 'فرمت JSON نادرست', -31 => 'مرچنت کد الزامی', -32 => 'مبلغ الزامی',
            -33 => 'Authority الزامی', -40 => 'Authority نامعتبر',
        ];
        $message = $errors[$code] ?? ($result['body']['errors'][0]['message'] ?? 'خطای ناشناخته در تایید');
        return ['success' => false, 'message' => $message, 'code' => $code];
    }
}
