<?php


class Image
{
    private $resource;
    private $color;
    private $angle;

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
    public function getAngle()
    {
        return $this->angle;
    }



    public function createFromPng($source)
    {
        $this->resource = imagecreatefrompng($source);
    }

    public function colorAllocate($color)
    {
        $rgbColor = $this->hex2rgb($color);
        $this->color = imagecolorallocate($this->resource,$rgbColor['r'],$rgbColor['g'],$rgbColor['b']);
    }

    public function generateAngle($angleMin,$angleMax)
    {
        $this->angle = mt_rand( $angleMin, $angleMax ) * (mt_rand(0, 1) == 1 ? -1 : 1);
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


}