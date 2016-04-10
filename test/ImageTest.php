<?php

include_once 'Image.php';
include_once 'Configuration.php';


class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf('Image',new Image());
    }

    public function testCreateFromPng()
    {
        $image = new Image();
        $configuration = new Configuration();

        $background = $configuration->obtainValue('backgrounds')[0];
        $image->createFromPng($background);

        $testImage = imagecreatefrompng($background);

        $this->assertTrue($this->image_compare($testImage, $image->getResource()));

        $otherImage = new Image();
        $otherBackground = $configuration->obtainValue('backgrounds')[1];
        $otherImage->createFromPng($otherBackground);

        $this->assertFalse($this->image_compare($testImage, $otherImage->getResource()));

    }


    private function image_compare($image1, $image2)
    {
        $im = $image1;
        $im2 = $image2;

        if (imagesx($im)!=imagesx($im2)) return false;
        if (imagesy($im)!=imagesy($im2)) return false;

        for ($width=0;$width<=imagesx($im)-1;$width++) {
            for ($height=0;$height<=imagesy($im)-1;$height++) {
                $rgb1 = imagecolorat($im, $width, $height);
                $rgb2 = imagecolorat($im2, $width, $height);

                if ($rgb1 != $rgb2) {
                    return false;
                }

            }
        }

        return true;
    }

}
