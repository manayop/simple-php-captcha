<?php



class Configuration
{
    const BACKGROUND_PATH = '/backgrounds/';
    const FONTS_PATH = '/fonts/';

    const MIN_LENGTH_KEY = 'min_length';

    private $config;

    public function __construct($config = array())
    {
        $config = $this->sanitize($config);

        $bg_path = $this->obtainUbication() . self::BACKGROUND_PATH;
        $font_path = $this->obtainUbication() . self::FONTS_PATH;

        $defaults = array(
            'code' => '',
            self::MIN_LENGTH_KEY => 5,
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
                $font_path . 'times_new_yorker.ttf'
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

        $this->config = array_merge($defaults,$config);

        $this->config = $this->sanitizeLimits($this->config);


    }

    private function sanitize($config)
    {
        $result = array();
        if (is_array($config)){
            $result = $config;
        }



        return $result;


    }

    private function sanitizeLimits($config)
    {
        $result = $config;
        if( $result['min_length'] < 1 ) $result['min_length'] = 1;
        if( $result['angle_min'] < 0 ) $result['angle_min'] = 0;
        if( $result['angle_max'] > 10 ) $result['angle_max'] = 10;
        if( $result['angle_max'] < $result['angle_min'] ) $result['angle_max'] = $result['angle_min'];
        if( $result['min_font_size'] < 10 ) $result['min_font_size'] = 10;
        if( $result['max_font_size'] < $result['min_font_size'] ) $result['max_font_size'] = $result['min_font_size'];

        return $result;
    }

    private function obtainUbication()
    {
        return dirname(__FILE__);

    }

    public function asHash()
    {
        return $this->config;
    }


    public function obtainMinLength()
    {
        return $this->config[self::MIN_LENGTH_KEY];
    }

}