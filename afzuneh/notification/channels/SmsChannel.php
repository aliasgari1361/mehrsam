<?php

class SmsChannel {
    protected $apiKey;
    protected $defaultPhone;

    public function __construct() {
        $this->apiKey = NOTIF_KAVENEGAR_API_KEY;
        $this->defaultPhone = NOTIF_KAVENEGAR_ADMIN_PHONE;
    }

    public function send($to, $message) {
        if (empty($this->apiKey) || empty($to)) return false;
        $url = "https://api.kavenegar.com/v1/{$this->apiKey}/sms/send.json";
        $data = ['receptor' => $to, 'message' => $message];
        return $this->call($url, $data);
    }

    public function notifyAdmin($message) {
        return $this->send($this->defaultPhone, $message);
    }

    protected function call($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
