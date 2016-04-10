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


    $image = new Image($configuration);
    $image->create();
    $image->colorAllocate();
    $image->generateAngle();
    $image->generateFontSize();
    $image->generateTextPosition();
    if( $configuration->obtainValue('shadow') ){
        $image->shadowColorAllocate();
        $image->writeBackgroundText(
            $image->getTextXPosition(),
            $image->getTextYPosition(),
            $image->getShadowColor()
        );
    }
    $image->writeText(
        $image->getTextXPosition(),
        $image->getTextYPosition(),
        $image->getColor()
    );

    header("Content-type: image/png");
    $image->generateOutput();


}