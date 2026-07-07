<?php

class WhatsAppChannel {
    protected $apiKey;
    protected $defaultPhone;

    public function __construct() {
        $this->apiKey = NOTIF_WHATSAPP_API_KEY;
        $this->defaultPhone = NOTIF_WHATSAPP_ADMIN_PHONE;
    }

    public function send($to, $message) {
        if (empty($this->apiKey) || empty($to)) return false;
        return $this->call($to, $message);
    }

    public function notifyAdmin($message) {
        return $this->send($this->defaultPhone, $message);
    }

    protected function call($to, $message) {
        // TODO: integration with a WhatsApp Business API provider
        return false;
    }
}
