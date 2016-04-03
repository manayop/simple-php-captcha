<?php

include_once "CaptchaGenerator.php";


class CaptchaGeneratorTest extends PHPUnit_Framework_TestCase
{

    public function testGenerator()
    {
        $this->assertInstanceOf('CaptchaGenerator',new CaptchaGenerator());
    }

    public function testConfiguration()
    {

    }

}
