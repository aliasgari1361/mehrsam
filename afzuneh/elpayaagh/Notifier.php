<?php

require_once __DIR__ . '/elpayaagh_tanzimat.php';
require_once __DIR__ . '/channels/kanal_telegram.php';
require_once __DIR__ . '/channels/kanal_bale.php';
require_once __DIR__ . '/channels/kanal_payamak.php';
require_once __DIR__ . '/channels/kanal_whatsapp.php';
require_once __DIR__ . '/channels/kanal_push.php';

class Notifier {
    protected $telegram;
    protected $bale;
    protected $sms;
    protected $whatsapp;
    protected $push;

    public function __construct() {
        $this->telegram = new kanal_telegram();
        $this->bale = new kanal_bale();
        $this->sms = new kanal_payamak();
        $this->whatsapp = new kanal_whatsapp();
        $this->push = new kanal_push();
    }

    public function notifyAdmin($message) {
        $results = [];
        $results['telegram'] = $this->telegram->notifyAdmin($message);
        $results['bale'] = $this->bale->notifyAdmin($message);
        /* اعلان گوشی (Web Push) — به همه دستگاههای ثبتشده */
        try {
            $results['push'] = $this->push->notifyAdmin('پیام جدید مهراد سام', strip_tags($message), '/mod/chat');
        } catch (\Throwable $e) {
            $results['push'] = ['ok' => false, 'error' => $e->getMessage()];
        }
        return $results;
    }

    public function sendToCustomer($channel, $to, $message) {
        switch ($channel) {
            case 'telegram':
                return $this->telegram->send($to, $message);
            case 'bale':
                return $this->bale->send($to, $message);
            case 'whatsapp':
                return $this->whatsapp->send($to, $message);
            case 'sms':
                return $this->sms->send($to, $message);
            default:
                return false;
        }
    }

    public static function notify($message) {
        $notifier = new self();
        return $notifier->notifyAdmin($message);
    }

    public static function newOrder($order_id, $total, $customer_name) {
        $message = "🛍 <b>سفارش جدید</b>\n";
        $message .= "شماره سفارش: #{$order_id}\n";
        $message .= "مشتری: {$customer_name}\n";
        $message .= "مبلغ: " . number_format($total) . " تومان\n";
        $message .= "زمان: " . date('Y-m-d H:i');
        return self::notify($message);
    }

    public static function newContactMessage($name, $phone, $subject) {
        $message = "✉️ <b>پیام جدید از فرم تماس</b>\n";
        $message .= "نام: {$name}\n";
        $message .= "تلفن: {$phone}\n";
        $message .= "موضوع: {$subject}\n";
        $message .= "زمان: " . date('Y-m-d H:i');
        return self::notify($message);
    }
}
