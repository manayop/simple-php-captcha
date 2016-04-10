<?php


include_once 'Configuration.php';
include_once 'CaptchaGenerator.php';
include_once 'UbicationHandler.php';
include_once 'ImageProperties.php';
include_once 'Image.php';
include_once 'LibraryTest.php';
include_once 'NativeSession.php';

function simple_php_captcha($config = array()) {

    $libraryTest = new LibraryTest();
    $libraryTest->functionTest('gd_info');

    $configuration = new Configuration($config);

    $captchaGenerator = new CaptchaGenerator($configuration);
    $captchaGenerator->generateCode();
    $captcha_config = $captchaGenerator->getConfiguration()->asHash();

    $session = new NativeSession();
    $session->serializedSetWithPrefix('_CAPTCHA','config',$captcha_config);


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

if( isset($_GET['_CAPTCHA']) ) {

    $session = new NativeSession();
    $session->start();
    $captcha_config = $session->serializedGetWithPrefix('_CAPTCHA','config');
    if( !$captcha_config ) exit();
    $configuration = new Configuration($captcha_config);

    unset($_SESSION['_CAPTCHA']);


    $image = new Image($configuration);
    $image->create();
    $image->draw();

    header("Content-type: image/png");
    $image->generateOutput();


}