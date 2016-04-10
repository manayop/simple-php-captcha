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

    public function testHex2RgbTransform()
    {
        $image = new Image();

        $color = $image->hex2rgb('#00F');

        $this->assertEquals($color,['r' => 0, 'g' => 0, 'b' => 255]);


    }

    public function testColorAllocate()
    {
        $image = new Image();
        $configuration = new Configuration();

        $background = $configuration->obtainValue('backgrounds')[0];
        $image->createFromPng($background);

        $color = '#00F';
        $rgbColor = $image->hex2rgb($color);

        $image->colorAllocate($color);
        $rgbColorTest = imagecolorsforindex($image->getResource(),$image->getColor());

        $this->assertEquals($rgbColor['r'], $rgbColorTest['red']);
        $this->assertEquals($rgbColor['g'], $rgbColorTest['green']);
        $this->assertEquals($rgbColor['b'], $rgbColorTest['blue']);
    }

    public function testShadowColorAllocate()
    {
        $image = new Image();
        $configuration = new Configuration();

        $background = $configuration->obtainValue('backgrounds')[0];
        $image->createFromPng($background);

        $color = '#00F';
        $rgbColor = $image->hex2rgb($color);

        $image->shadowColorAllocate($color);
        $rgbColorTest = imagecolorsforindex($image->getResource(),$image->getShadowColor());

        $this->assertEquals($rgbColor['r'], $rgbColorTest['red']);
        $this->assertEquals($rgbColor['g'], $rgbColorTest['green']);
        $this->assertEquals($rgbColor['b'], $rgbColorTest['blue']);
    }


    public function testAngle()
    {
        $image = new Image();

        $image->generateAngle(0,10);

        $angle = $image->getAngle();

        $this->assertGreaterThanOrEqual($angle,10);
        $this->assertGreaterThanOrEqual(-10,$angle);
    }

    public function testFontSize()
    {
        $image = new Image();

        $image->generateFontSize(0,10);

        $font_size = $image->getFontSize();

        $this->assertGreaterThanOrEqual($font_size,10);
        $this->assertGreaterThanOrEqual(0,$font_size);
    }

    public function testTextPosition()
    {
        $image = new Image();
        $configuration = new Configuration();

        $background = $configuration->obtainValue('backgrounds')[0];
        $font = $configuration->obtainValue('fonts')[0];
        $code = "1234";

        $image->createFromPng($background);
        $imageProperties = new ImageProperties();
        list($bg_width, $bg_height) = $imageProperties->getImageSize($background);

        $image->generateTextPosition($bg_width,$bg_height,$font,$code);
        $x = $image->getTextXPosition();
        $y = $image->getTextYPosition();

        $this->assertGreaterThanOrEqual($x,$bg_width);
        $this->assertGreaterThanOrEqual($y,$bg_height);

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
