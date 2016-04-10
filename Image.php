<?php


class Image
{
    private $resource;

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    public function createFromPng($source)
    {
        $this->resource = imagecreatefrompng($source);
    }


}