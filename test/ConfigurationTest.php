<?php

include_once 'Configuration.php';

class ConfigurationTest extends PHPUnit_Framework_TestCase
{

    private $defaults = array(
        'code' => '',
        'min_length' => 5,
        'max_length' => 5,
        'backgrounds' => array(
            '/app/backgrounds/45-degree-fabric.png',
            '/app/backgrounds/cloth-alike.png',
            '/app/backgrounds/grey-sandbag.png',
            '/app/backgrounds/kinda-jean.png',
            '/app/backgrounds/polyester-lite.png',
            '/app/backgrounds/stitched-wool.png',
            '/app/backgrounds/white-carbon.png',
            '/app/backgrounds/white-wave.png'
        ),
        'fonts' => array(
            '/app/fonts/times_new_yorker.ttf'
        ),
        'characters' => 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghjkmnprstuvwxyz23456789',
        'min_font_size' => 28,
        'max_font_size' => 28,
        'color' => '#666',
        'angle_min' => 0,
        'angle_max' => 10,
        'shadow' => true,
        'shadow_color' => '#fff',
        'shadow_offset_x' => -1,
        'shadow_offset_y' => 1
    );

    public function testOpts()
    {
        $this->assertInstanceOf('Configuration',new Configuration());
    }

    public function testDefaults()
    {
        $configuration = new Configuration();
        $asHash = $configuration->asHash();
        $this->assertEquals($this->defaults, $asHash);

    }

    public function testNullConfiguration()
    {
        $nullConfiguration = new Configuration(null);
        $this->assertEquals($this->defaults, $nullConfiguration->asHash());
    }

    public function testDefaultsNotOverwrite()
    {
        $notNullConfiguration = new Configuration(array(
            min_font_size => 10,
            max_font_size => 11
        ));

        $this->assertEquals($notNullConfiguration->asHash()['min_font_size'], 10);
        $this->assertEquals($notNullConfiguration->asHash()['max_font_size'], 11);



    }

    public function testObtainValue()
    {
        $configuration = new Configuration();

        $this->assertEquals(5,$configuration->obtainValue('min_length'));
        $this->assertEquals('#666',$configuration->obtainValue('color'));

    }

    public function testSettingValue()
    {
        $configuration = new Configuration();

        $configuration->setValue('code','1234');
        $this->assertEquals('1234',$configuration->obtainValue('code'));

        $configuration->setValue('code','aaaa');
        $this->assertEquals('aaaa',$configuration->obtainValue('code'));

    }
    public function testSanitizeLimits()
    {
        $maxMinLimitsConfiguration = new Configuration(array(
            'min_length' => 0,
            'angle_min' => -1,
            'angle_max' => 11,
            'min_font_size' => 9
        ));

        $this->assertEquals($maxMinLimitsConfiguration->asHash()['min_length'],1);

        $this->assertEquals($maxMinLimitsConfiguration->asHash()['angle_min'],0);
        $this->assertEquals($maxMinLimitsConfiguration->asHash()['angle_max'],10);
        $this->assertEquals($maxMinLimitsConfiguration->asHash()['min_font_size'],10);

        $comparisonLimitsConfiguration = new Configuration(array(
            'angle_min' => 6,
            'angle_max' => 5,
            'min_font_size' => 9,
            'max_font_size' => 8
        ));

        $this->assertEquals($comparisonLimitsConfiguration->asHash()['angle_min'],$comparisonLimitsConfiguration->asHash()['angle_max']);
        $this->assertEquals($comparisonLimitsConfiguration->asHash()['min_font_size'],$comparisonLimitsConfiguration->asHash()['max_font_size']);


    }

    public function testPickRandomBackground()
    {
        $configuration = new Configuration();

        $randomBackground = $configuration->obtainRandomBackground();

        $this->assertContains($randomBackground,$configuration->obtainValue('backgrounds'));
    }

    public function testPickRandomFont()
    {
        $configuration = new Configuration();

        $randomFont = $configuration->obtainRandomFont();

        $this->assertContains($randomFont,$configuration->obtainValue('fonts'));
    }


}
