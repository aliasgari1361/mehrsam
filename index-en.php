<?php
/**
 * Application entry point
 * Loads both legacy (Persian) and new (English) file structures
 */

// Try English path first, fallback to Persian
$config_file = __DIR__ . '/haste/pikan_tanzimat.php';
$legacy_config = __DIR__ . '/haste/tanzimat.php';

if (file_exists($config_file)) {
    require_once $config_file;
} else {
    require_once $legacy_config;
}

// Load helper functions
require_once __DIR__ . '/haste/tavabe.php';

// Load router
require_once __DIR__ . '/haste/masiryab.php';

// Load language system if available
if (file_exists(__DIR__ . '/haste/languages.php')) {
    require_once __DIR__ . '/haste/languages.php';
}

// Get URL parameter
$url = $_GET['url'] ?? 'home';
$url = filter_var($url, FILTER_SANITIZE_URL);

// Run router
masiryab_kon($url);