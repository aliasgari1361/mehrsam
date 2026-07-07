<?php
/**
 * مدیریت درگاه‌های پرداخت
 * درگاه مناسب را بر اساس تنظیمات site_settings.json انتخاب می‌کند
 */
require_once __DIR__ . '/PaymentGateway.php';
require_once __DIR__ . '/ZarinPalGateway.php';
require_once __DIR__ . '/IdPayGateway.php';
require_once __DIR__ . '/ZibalGateway.php';

class GatewayManager {
    private static $gateways = [
        'zarinpal' => ZarinPalGateway::class,
        'idpay'    => IdPayGateway::class,
        'zibal'    => ZibalGateway::class,
    ];

    /** لیست درگاه‌های فعال */
    public static function getEnabled() {
        require_once MASIR_RISH . 'haste/site_settings.php';
        $gateways = get_site_setting('gateways') ?? [];
        $enabled = [];
        foreach ($gateways as $key => $settings) {
            if (!empty($settings['enabled']) && isset(self::$gateways[$key])) {
                $enabled[$key] = new self::$gateways[$key]($settings);
            }
        }
        return $enabled;
    }

    /** دریافت یک درگاه مشخص */
    public static function get($key) {
        require_once MASIR_RISH . 'haste/site_settings.php';
        $settings = get_site_setting("gateways.$key") ?? [];
        if (!isset(self::$gateways[$key])) {
            return null;
        }
        return new self::$gateways[$key]($settings);
    }

    /** اولین درگاه فعال */
    public static function first() {
        $enabled = self::getEnabled();
        return !empty($enabled) ? reset($enabled) : null;
    }
}
