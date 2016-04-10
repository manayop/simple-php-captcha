<?php


class Image
{
    private $configuration;
    private $resource;
    private $color;
    private $shadowColor;
    private $angle;
    private $fontSize;
    private $textXPosition;
    private $textYPosition;

    public function __construct($configuration)
    {
        if (!$configuration instanceof Configuration){
            throw new InvalidArgumentException();
        }

        $this->configuration = $configuration;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return mixed
     */
    public function getShadowColor()
    {
        return $this->shadowColor;
    }


    /**
     * @return mixed
     */
    public function getAngle()
    {
        return $this->angle;
    }

    /**
     * @return mixed
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * @return mixed
     */
    public function getTextXPosition()
    {
        return $this->textXPosition;
    }

    /**
     * @return mixed
     */
    public function getTextYPosition()
    {
        return $this->textYPosition;
    }



    public function createFromPng($source)
    {
        $this->resource = imagecreatefrompng($source);
    }

    public function colorAllocate()
    {
        $rgbColor = $this->hex2rgb($this->configuration->obtainValue('color'));
        $this->color = imagecolorallocate($this->resource,$rgbColor['r'],$rgbColor['g'],$rgbColor['b']);
    }

    public function shadowColorAllocate()
    {
        $rgbColor = $this->hex2rgb($this->configuration->obtainValue('shadow_color'));
        $this->shadowColor = imagecolorallocate($this->resource,$rgbColor['r'],$rgbColor['g'],$rgbColor['b']);
    }

    public function generateAngle()
    {
        $angleMin = $this->configuration->obtainValue('angle_min');
        $angleMax = $this->configuration->obtainValue('angle_max');
        $this->angle = mt_rand( $angleMin, $angleMax ) * (mt_rand(0, 1) == 1 ? -1 : 1);
    }

    public function generateFontSize()
    {
        $sizeMin = $this->configuration->obtainValue('min_font_size');
        $sizeMax = $this->configuration->obtainValue('max_font_size');

        $this->fontSize = mt_rand($sizeMin,$sizeMax);
    }

    public function generateTextPosition($containerWidth,$containerHeight,$font)
    {
        $code = $this->configuration->obtainValue('code');
        $text_box_size = imagettfbbox($this->fontSize, $this->angle, $font, $code);

        $box_width = abs($text_box_size[6] - $text_box_size[2]);
        $box_height = abs($text_box_size[5] - $text_box_size[1]);
        $text_pos_x_min = 0;
        $text_pos_x_max = ($containerWidth) - ($box_width);
        $this->textXPosition = mt_rand($text_pos_x_min, $text_pos_x_max);

        $text_pos_y_min = $box_height;
        $text_pos_y_max = ($containerHeight) - ($box_height / 2);
        if ($text_pos_y_min > $text_pos_y_max) {
            $temp_text_pos_y = $text_pos_y_min;
            $text_pos_y_min = $text_pos_y_max;
            $text_pos_y_max = $temp_text_pos_y;
        }
        $this->textYPosition = mt_rand($text_pos_y_min, $text_pos_y_max);
    }

    public function hex2rgb($hex_str, $return_string = false, $separator = ',') {

        $hex_str = preg_replace("/[^0-9A-Fa-f]/", '', $hex_str); // Gets a proper hex string
        $rgb_array = array();
        if( strlen($hex_str) == 6 ) {
            $color_val = hexdec($hex_str);
            $rgb_array['r'] = 0xFF & ($color_val >> 0x10);
            $rgb_array['g'] = 0xFF & ($color_val >> 0x8);
            $rgb_array['b'] = 0xFF & $color_val;
        } elseif( strlen($hex_str) == 3 ) {
            $rgb_array['r'] = hexdec(str_repeat(substr($hex_str, 0, 1), 2));
            $rgb_array['g'] = hexdec(str_repeat(substr($hex_str, 1, 1), 2));
            $rgb_array['b'] = hexdec(str_repeat(substr($hex_str, 2, 1), 2));
        } else {
            return false;
        }
        return $return_string ? implode($separator, $rgb_array) : $rgb_array;

    }

    public function writeText($textXPosition,$textYPosition,$color,$font)
    {
        $code = $this->configuration->obtainValue('code');
        imagettftext($this->resource, $this->fontSize, $this->angle, $textXPosition, $textYPosition, $color, $font, $code);

    }

    public function writeBackgroundText($textXPosition,$textYPosition,$color,$font)
    {
        $backgroundXOffset = $this->configuration->obtainValue('shadow_offset_x');
        $backgroundYOffset = $this->configuration->obtainValue('shadow_offset_y');
        $code = $this->configuration->obtainValue('code');

        imagettftext($this->resource, $this->fontSize, $this->angle, $textXPosition + $backgroundXOffset, $textYPosition + $backgroundYOffset, $color, $font, $code);

    }

    public function generateOutput()
    {
        imagepng($this->resource);
    }


}