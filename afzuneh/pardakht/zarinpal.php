<?php
/**
 * درگاه پرداخت زرین‌پال
 */
class ZarinPal {
    private $merchant_id;
    private $sandbox;
    private $api_url;
    private $callback_url;
    private $startpay_url;
    private $verify_url;

    public function __construct() {
        $this->merchant_id = defined('ZARINPAL_MERCHANT') ? ZARINPAL_MERCHANT : '';
        $this->sandbox = defined('ZARINPAL_SANDBOX') ? ZARINPAL_SANDBOX : true;
        $this->callback_url = BASE_URL . '/forushgah/checkout/zarinpal/callback';
        
        if ($this->sandbox) {
            $this->api_url = 'https://sandbox.zarinpal.com/pg/v4/payment/request.json';
            $this->verify_url = 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json';
            $this->startpay_url = 'https://sandbox.zarinpal.com/pg/StartPay/';
        } else {
            $this->api_url = 'https://api.zarinpal.com/pg/v4/payment/request.json';
            $this->verify_url = 'https://api.zarinpal.com/pg/v4/payment/verify.json';
            $this->startpay_url = 'https://www.zarinpal.com/pg/StartPay/';
        }
    }

    public function request($amount, $order_id, $description = 'پرداخت سفارش') {
        if (empty($this->merchant_id)) {
            return ['success' => false, 'message' => 'مرچنت کد زرین‌پال تنظیم نشده است'];
        }

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

        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($http_code === 200 && isset($result['data']['code']) && $result['data']['code'] === 100) {
            return [
                'success' => true,
                'authority' => $result['data']['authority'],
                'redirect_url' => $this->startpay_url . $result['data']['authority']
            ];
        }

        $errors = [
            -9 => 'خطای اعتبارسنجی',
            -10 => 'ترمینال تایید نشده',
            -11 => 'مرچنت کد صحیح نیست',
            -12 => 'مرچنت کد فعال نیست',
            -15 => 'ترمینال معلق شده',
            -16 => 'سطح تایید پایین',
            -17 => 'مبلغ نادرست',
            -18 => 'مبلغ کمتر از حداقل',
            -19 => 'مبلغ بیشتر از حداکثر',
            -20 => 'مبلغ پرداختی با مبلغ درخواستی متفاوت',
            -21 => 'پرداخت یافت نشد',
            -22 => 'تراکنش ناموفق',
            -23 => 'خطای درگاه',
            -30 => 'فرمت JSON نادرست',
            -31 => 'مرچنت کد الزامی',
            -32 => 'مبلغ الزامی',
            -33 => 'Callback URL الزامی',
            -34 => 'Description الزامی',
            -40 => 'متادیتا معتبر نیست',
            -41 => 'موبایل معتبر نیست',
            -42 => 'ایمیل معتبر نیست',
            -50 => 'Callback URL معتبر نیست',
            -51 => 'Description طولانی است',
            -52 => 'موبایل طولانی است',
            -53 => 'ایمیل طولانی است',
            -54 => 'Description خالی است',
        ];

        $code = $result['data']['code'] ?? -1;
        $message = $errors[$code] ?? ($result['errors'][0]['message'] ?? 'خطای ناشناخته');

        return ['success' => false, 'message' => $message, 'code' => $code];
    }

    public function verify($amount, $authority) {
        if (empty($this->merchant_id)) {
            return ['success' => false, 'message' => 'مرچنت کد زرین‌پال تنظیم نشده است'];
        }

        $data = [
            'merchant_id' => $this->merchant_id,
            'amount' => (int)$amount,
            'authority' => $authority
        ];

        $ch = curl_init($this->verify_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($http_code === 200 && isset($result['data']['code'])) {
            if ($result['data']['code'] === 100 || $result['data']['code'] === 101) {
                return [
                    'success' => true,
                    'ref_id' => $result['data']['ref_id'],
                    'card_pan' => $result['data']['card_pan'] ?? '',
                    'card_hash' => $result['data']['card_hash'] ?? '',
                    'fee' => $result['data']['fee'] ?? 0,
                    'fee_type' => $result['data']['fee_type'] ?? '',
                    'message' => $result['data']['code'] === 101 ? 'تراکنش تکراری (قبلاً تایید شده)' : 'پرداخت با موفقیت انجام شد'
                ];
            }
        }

        $errors = [
            -9 => 'خطای اعتبارسنجی',
            -10 => 'ترمینال تایید نشده',
            -11 => 'مرچنت کد صحیح نیست',
            -12 => 'مرچنت کد فعال نیست',
            -13 => 'پرداخت یافت نشد',
            -14 => 'مرچنت کد معتبر نیست',
            -15 => 'ترمینال معلق شده',
            -16 => 'سطح تایید پایین',
            -17 => 'مبلغ نادرست',
            -18 => 'مبلغ کمتر از حداقل',
            -19 => 'مبلغ بیشتر از حداکثر',
            -20 => 'مبلغ پرداختی با مبلغ درخواستی متفاوت',
            -21 => 'پرداخت یافت نشد',
            -22 => 'تراکنش ناموفق',
            -23 => 'خطای درگاه',
            -30 => 'فرمت JSON نادرست',
            -31 => 'مرچنت کد الزامی',
            -32 => 'مبلغ الزامی',
            -33 => 'Authority الزامی',
            -40 => 'Authority نامعتبر',
        ];

        $code = $result['data']['code'] ?? -1;
        $message = $errors[$code] ?? ($result['errors'][0]['message'] ?? 'خطای ناشناخته در تایید');

        return ['success' => false, 'message' => $message, 'code' => $code];
    }
}