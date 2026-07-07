<?php
/**
 * Simple CAPTCHA for LaRaGoRn
 */

function generate_captcha() {
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= rand(0, 9);
    }
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['captcha_code'] = $code;
    $_SESSION['captcha_time'] = time();
    
    return $code;
}

function verify_captcha($input) {
    if (!isset($_SESSION['captcha_code'])) {
        return false;
    }
    
    // منقضی شدن (5 دقیقه)
    if (time() - ($_SESSION['captcha_time'] ?? 0) > 300) {
        unset($_SESSION['captcha_code'], $_SESSION['captcha_time']);
        return false;
    }
    
    $valid = strtoupper(trim($input)) === strtoupper($_SESSION['captcha_code']);
    if ($valid) {
        unset($_SESSION['captcha_code'], $_SESSION['captcha_time']);
    }
    return $valid;
}

function display_captcha_image() {
    $code = generate_captcha();
    
    // Try GD first, fallback to SVG
    if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
        header('Content-Type: image/png');
        
        $width = 120;
        $height = 40;
        $image = imagecreatetruecolor($width, $height);
        
        $bg = imagecolorallocate($image, 240, 240, 240);
        imagefilledrectangle($image, 0, 0, $width, $height, $bg);
        
        $text_color = imagecolorallocate($image, 50, 50, 50);
        $font_size = 5;
        
        $text_width = imagefontwidth($font_size) * strlen($code);
        $text_height = imagefontheight($font_size);
        $x = ($width - $text_width) / 2;
        $y = ($height - $text_height) / 2;
        
        imagestring($image, $font_size, (int)$x, (int)$y, $code, $text_color);
        
        // خطوط نویز
        for ($i = 0; $i < 5; $i++) {
            $line_color = imagecolorallocate($image, rand(180, 220), rand(180, 220), rand(180, 220));
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $line_color);
        }
        
        imagepng($image);
        imagedestroy($image);
    } else {
        // SVG fallback - no dependencies
        header('Content-Type: image/svg+xml');
        
        // تولید SVG ساده
        $noise = '';
        for ($i = 0; $i < 20; $i++) {
            $x1 = rand(0, 100);
            $y1 = rand(0, 30);
            $x2 = rand(0, 100);
            $y2 = rand(0, 30);
            $noise .= "<line x1='$x1' y1='$y1' x2='$x2' y2='$y2' stroke='#ddd' stroke-width='1'/>";
        }
        
        echo "<?xml version='1.0' encoding='UTF-8'?>";
        echo "<svg xmlns='http://www.w3.org/2000/svg' width='100' height='30'>";
        echo "<rect width='100' height='30' fill='#f0f0f0'/>";
        echo "<text x='50' y='20' font-family='monospace' font-size='14' fill='#333' text-anchor='middle'>{$code}</text>";
        echo $noise;
        echo "</svg>";
    }
    exit;
}