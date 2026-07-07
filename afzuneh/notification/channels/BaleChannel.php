<?php

class BaleChannel {
    protected $botToken;
    protected $defaultChatId;

    public function __construct() {
        $this->botToken = NOTIF_BALE_BOT_TOKEN;
        $this->defaultChatId = NOTIF_BALE_ADMIN_ID;
    }

    public function send($to, $message) {
        if (empty($this->botToken) || empty($to)) return false;
        $url = "https://tapi.bale.ai/bot{$this->botToken}/sendMessage";
        $data = ['chat_id' => $to, 'text' => $message, 'parse_mode' => 'HTML'];
        return $this->call($url, $data);
    }

    public function notifyAdmin($message) {
        return $this->send($this->defaultChatId, $message);
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
