<?php
/**
 * Autoloader for LaRaGoRn Framework
 * Supports both Persian (original) and English (alias) file paths
 */

spl_autoload_register(function($class) {
    $aliases = [
        'Config' => 'haste/tanzimat.php',
        'Router' => 'haste/masiryab.php',
        'Helpers' => 'haste/tavabe.php',
        'AdminController' => 'haste/mod/mod.php',
        'ProductController' => 'mohtava/forushgah/mahsul-kontrol.php',
        'CartController' => 'mohtava/forushgah/sabad-kontrol.php',
        'OrderController' => 'mohtava/forushgah/sefaresh-kontrol.php',
        'SeoHelper' => 'mohtava/seo-settings.php',
    ];
    
    if (isset($aliases[$class])) {
        require_once __DIR__ . '/' . $aliases[$class];
    }
});

// Load core files
require_once __DIR__ . '/haste/tanzimat.php';
require_once __DIR__ . '/haste/tavabe.php';

// Load language system
if (file_exists(__DIR__ . '/haste/languages.php')) {
    require_once __DIR__ . '/haste/languages.php';
}