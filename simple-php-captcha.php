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
    $imageProperties = new ImageProperties();
    list($bg_width, $bg_height, $bg_type, $bg_attr) = $imageProperties->getImageSize($background);

    $image = new Image();
    $image->createFromPng($background);
    $image->colorAllocate($configuration->obtainValue('color'));
    $image->generateAngle($configuration->obtainValue('angle_min'),$configuration->obtainValue('angle_max'));
    $captcha = $image->getResource();


    // Select font randomly
    $font = $captcha_config['fonts'][mt_rand(0, count($captcha_config['fonts']) - 1)];

    // Verify font file exists
    if( !file_exists($font) ) throw new Exception('Font file not found: ' . $font);

    //Set the font size.
    $font_size = mt_rand($captcha_config['min_font_size'], $captcha_config['max_font_size']);
    $text_box_size = imagettfbbox($font_size, $image->getAngle(), $font, $captcha_config['code']);

    // Determine text position
    $box_width = abs($text_box_size[6] - $text_box_size[2]);
    $box_height = abs($text_box_size[5] - $text_box_size[1]);
    $text_pos_x_min = 0;
    $text_pos_x_max = ($bg_width) - ($box_width);
    $text_pos_x = mt_rand($text_pos_x_min, $text_pos_x_max);
    $text_pos_y_min = $box_height;
    $text_pos_y_max = ($bg_height) - ($box_height / 2);
    if ($text_pos_y_min > $text_pos_y_max) {
        $temp_text_pos_y = $text_pos_y_min;
        $text_pos_y_min = $text_pos_y_max;
        $text_pos_y_max = $temp_text_pos_y;
    }
    $text_pos_y = mt_rand($text_pos_y_min, $text_pos_y_max);

    // Draw shadow
    if( $captcha_config['shadow'] ){
        $shadow_color = hex2rgb($captcha_config['shadow_color']);
        $shadow_color = imagecolorallocate($captcha, $shadow_color['r'], $shadow_color['g'], $shadow_color['b']);
        imagettftext($captcha, $font_size, $image->getAngle(), $text_pos_x + $captcha_config['shadow_offset_x'], $text_pos_y + $captcha_config['shadow_offset_y'], $shadow_color, $font, $captcha_config['code']);
    }

    // Draw text
    imagettftext($captcha, $font_size, $image->getAngle(), $text_pos_x, $text_pos_y, $image->getColor(), $font, $captcha_config['code']);

    // Output image
    header("Content-type: image/png");
    imagepng($captcha);

}