<?php


include_once 'Configuration.php';
include_once 'CaptchaGenerator.php';
include_once 'UbicationHandler.php';
include_once 'ImageProperties.php';
include_once 'Image.php';

function simple_php_captcha($config = array()) {

    // Check for GD library
    if( !function_exists('gd_info') ) {
        throw new Exception('Required GD library is missing');
    }

    $configuration = new Configuration($config);

    $captchaGenerator = new CaptchaGenerator($configuration);
    $captchaGenerator->generateCode();
    $captcha_config = $captchaGenerator->getConfiguration()->asHash();
    $_SESSION['_CAPTCHA']['config'] = serialize($captcha_config);


    $ubicationHandler = new UbicationHandler();
    if ( $ubicationHandler->isActualScriptInDocumentPath() ) {
        $image_src = $captchaGenerator->generateHTMLImageSource($ubicationHandler->obtainUbication(__FILE__,$ubicationHandler->obtainDocumentRoot()));
    } else {
        $image_src = $captchaGenerator->generateHTMLImageSource($ubicationHandler->obtainUbication(__FILE__,$ubicationHandler->obtainActualScriptPath()));
    }

    return array(
        'code' => $captcha_config['code'],
        'image_src' => $image_src
    );

}


if( !function_exists('hex2rgb') ) {
    function hex2rgb($hex_str, $return_string = false, $separator = ',') {
        $hex_str = preg_replace("/[^0-9A-Fa-f]/", '', $hex_str); // Gets a proper hex string
        $rgb_array = array();
        if( strlen($hex_str) == 6 ) {
            $color_val = hexdec($hex_str);
            $rgb_array['r'] = 0xFF & ($color_val >> 0x10);
            $rgb_array['g'] = 0xFF & ($color_val >> 0x8);
            $rgb_array['b'] = 0xFF & $color_val;
        } elseif( strlen($hex_str) == 3 ) {
            $rgb_array['r'] = hexdec(str_repeat(substr($hex_str, 0, 1), 2));
            $rgb_array['g'] = hexdec(str_repeat(substr($hex_str, 1, 1), 2));
            $rgb_array['b'] = hexdec(str_repeat(substr($hex_str, 2, 1), 2));
        } else {
            return false;
        }
        return $return_string ? implode($separator, $rgb_array) : $rgb_array;
    }
}

// Draw the image
if( isset($_GET['_CAPTCHA']) ) {

    session_start();

    $captcha_config = unserialize($_SESSION['_CAPTCHA']['config']);
    if( !$captcha_config ) exit();
    $configuration = new Configuration($captcha_config);

    unset($_SESSION['_CAPTCHA']);

    $background = $configuration->obtainRandomBackground();
    $font = $configuration->obtainRandomFont();
    $imageProperties = new ImageProperties();
    list($bg_width, $bg_height, $bg_type, $bg_attr) = $imageProperties->getImageSize($background);

    $image = new Image();
    $image->createFromPng($background);
    $image->colorAllocate($configuration->obtainValue('color'));
    $image->generateAngle($configuration->obtainValue('angle_min'),$configuration->obtainValue('angle_max'));
    $image->generateFontSize($configuration->obtainValue('min_font_size'),$configuration->obtainValue('max_font_size'));
    $image->generateTextPosition($bg_width,$bg_height,$font,$configuration->obtainValue('code'));
    $captcha = $image->getResource();


    // Draw shadow
    if( $captcha_config['shadow'] ){
        $shadow_color = hex2rgb($captcha_config['shadow_color']);
        $shadow_color = imagecolorallocate($captcha, $shadow_color['r'], $shadow_color['g'], $shadow_color['b']);
        imagettftext($captcha, $image->getFontSize(), $image->getAngle(), $image->getTextXPosition() + $captcha_config['shadow_offset_x'], $image->getTextYPosition() + $captcha_config['shadow_offset_y'], $shadow_color, $font, $captcha_config['code']);
    }

    // Draw text
    imagettftext($captcha, $image->getFontSize(), $image->getAngle(), $image->getTextXPosition(), $image->getTextYPosition(), $image->getColor(), $font, $captcha_config['code']);

    // Output image
    header("Content-type: image/png");
    imagepng($captcha);

}