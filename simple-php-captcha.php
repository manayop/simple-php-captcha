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
    if( $configuration->obtainValue('shadow') ){
        $image->shadowColorAllocate($configuration->obtainValue('shadow_color'));
        $image->writeText(
            $image->getTextXPosition() + $configuration->obtainValue('shadow_offset_x'),
            $image->getTextYPosition() + $configuration->obtainValue('shadow_offset_y'),
            $image->getShadowColor(),
            $font,
            $configuration->obtainValue('code')
        );
    }
    $image->writeText(
        $image->getTextXPosition(),
        $image->getTextYPosition(),
        $image->getColor(),
        $font,
        $configuration->obtainValue('code')
    );

    header("Content-type: image/png");
    $image->generateOutput();


}