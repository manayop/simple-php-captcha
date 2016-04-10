<?php


class ImageProperties
{

    public function getImageSize($image)
    {
        return getimagesize($image);
    }



}