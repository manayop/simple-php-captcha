<?php

include_once 'Configuration.php';

class Test extends PHPUnit_Framework_TestCase
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


}
