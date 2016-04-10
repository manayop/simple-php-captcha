<?php

include_once "ImageProperties.php";
include_once "Configuration.php";

class ImagePropertiesTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf('ImageProperties',new ImageProperties());
    }

    public function testGetImageSize()
    {
        $imageProperties = new ImageProperties();

        $configuration = new Configuration();
        $background = $configuration->obtainRandomBackground();

        $this->assertEquals($imageProperties->getImageSize($background),getimagesize($background));
    }


}
