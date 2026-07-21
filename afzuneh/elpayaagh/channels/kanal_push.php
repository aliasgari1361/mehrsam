<?php

class kanal_push {
    public function __construct() {
    }

    public function send($to, $title, $body) {
        // TODO: PWA Web Push API integration
        return false;
    }

    public function notifyAdmin($title, $body) {
        return $this->send(null, $title, $body);
    }
}
