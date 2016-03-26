<?php

include_once 'Configuration.php';

class Test extends PHPUnit_Framework_TestCase
{

    public function testOpts()
    {
        $this->assertInstanceOf('Configuration',new Configuration());
    }

    public function testDefaults()
    {
        $configuration = new Configuration();

        $bg_path = '/app/backgrounds/';

        $defaults = array(
            'code' => '',
            'min_length' => 5,
            'max_length' => 5,
            'backgrounds' => array(
                $bg_path . '45-degree-fabric.png',
                $bg_path . 'cloth-alike.png',
                $bg_path . 'grey-sandbag.png',
                $bg_path . 'kinda-jean.png',
                $bg_path . 'polyester-lite.png',
                $bg_path . 'stitched-wool.png',
                $bg_path . 'white-carbon.png',
                $bg_path . 'white-wave.png'
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

        $asHash = $configuration->asHash();
        $this->assertEquals($defaults,$asHash);

        $nullConfiguration = new Configuration(null);
        $this->assertEquals($defaults,$nullConfiguration->asHash());

    }

}
