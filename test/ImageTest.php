<?php

include_once 'Image.php';
include_once 'Configuration.php';


class ImageTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf('Image',new Image(new Configuration()));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRequiredCollaboration()
    {
        $imageWithoutParameters = new Image('abc');

    }

    public function testCreation()
    {
        $configuration = new Configuration();
        $background = $configuration->obtainValue('backgrounds')[0];

        $oneBackgroundConfiguration = new Configuration(array('backgrounds' => array($background)));
        $image = new Image($oneBackgroundConfiguration);

        $image->create($background);

        $testImage = imagecreatefrompng($background);

        $this->assertTrue($this->image_compare($testImage, $image->getResource()));

        $otherImage = new Image($configuration);
        $otherBackground = $configuration->obtainValue('backgrounds')[1];
        $otherImage->create($otherBackground);

        $this->assertFalse($this->image_compare($testImage, $otherImage->getResource()));

    }

    public function testHex2RgbTransform()
    {
        $image = new Image(new Configuration());

        $color = $image->hex2rgb('#00F');

        $this->assertEquals($color,['r' => 0, 'g' => 0, 'b' => 255]);


    }

    public function testColorAllocate()
    {
        $color = '#00F';
        $configuration = new Configuration(array('color' => $color));
        $image = new Image($configuration);

        $background = $configuration->obtainValue('backgrounds')[0];
        $image->create($background);

        $rgbColor = $image->hex2rgb($color);

        $image->colorAllocate();
        $rgbColorTest = imagecolorsforindex($image->getResource(),$image->getColor());

        $this->assertEquals($rgbColor['r'], $rgbColorTest['red']);
        $this->assertEquals($rgbColor['g'], $rgbColorTest['green']);
        $this->assertEquals($rgbColor['b'], $rgbColorTest['blue']);
    }

    public function testShadowColorAllocate()
    {
        $color = '#00F';
        $configuration = new Configuration(array('shadow_color' => $color));
        $image = new Image($configuration);

        $background = $configuration->obtainValue('backgrounds')[0];
        $image->create($background);

        $rgbColor = $image->hex2rgb($color);

        $image->shadowColorAllocate();
        $rgbColorTest = imagecolorsforindex($image->getResource(),$image->getShadowColor());

        $this->assertEquals($rgbColor['r'], $rgbColorTest['red']);
        $this->assertEquals($rgbColor['g'], $rgbColorTest['green']);
        $this->assertEquals($rgbColor['b'], $rgbColorTest['blue']);
    }


    public function testAngle()
    {
        $image = new Image(new Configuration(array('angle_min' => 0, 'angle_max' => 5)));

        $image->generateAngle();

        $angle = $image->getAngle();

        $this->assertGreaterThanOrEqual($angle,5);
        $this->assertGreaterThanOrEqual(-5,$angle);
    }

    public function testFontSize()
    {
        $image = new Image(new Configuration(array('min_font_size' => 0, 'max_font_size' => 10)));
        $image->generateFontSize();

        $font_size = $image->getFontSize();

        $this->assertGreaterThanOrEqual($font_size,10);
        $this->assertGreaterThanOrEqual(0,$font_size);
    }

    public function testTextPosition()
    {
        $code = "1234";
        $configuration = new Configuration(array());
        $background = $configuration->obtainValue('backgrounds')[0];
        $font = $configuration->obtainValue('fonts')[0];

        $oneBackgroundConfiguration = new Configuration(
            array(
                'backgrounds' => array($background),
                'code' => $code
            )
        );
        $image = new Image($oneBackgroundConfiguration);


        $image->create();
        $imageProperties = new ImageProperties();
        list($bg_width, $bg_height) = $imageProperties->getImageSize($background);

        $image->generateTextPosition($font);
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
