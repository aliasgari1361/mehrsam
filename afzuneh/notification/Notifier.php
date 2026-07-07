<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/channels/TelegramChannel.php';
require_once __DIR__ . '/channels/BaleChannel.php';
require_once __DIR__ . '/channels/SmsChannel.php';
require_once __DIR__ . '/channels/WhatsAppChannel.php';
require_once __DIR__ . '/channels/PushChannel.php';

class Notifier {
    protected $telegram;
    protected $bale;
    protected $sms;
    protected $whatsapp;
    protected $push;

    public function __construct() {
        $this->telegram = new TelegramChannel();
        $this->bale = new BaleChannel();
        $this->sms = new SmsChannel();
        $this->whatsapp = new WhatsAppChannel();
        $this->push = new PushChannel();
    }

    public function notifyAdmin($message) {
        $results = [];
        $results['telegram'] = $this->telegram->notifyAdmin($message);
        $results['bale'] = $this->bale->notifyAdmin($message);
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
