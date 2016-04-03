<?php

include_once "CaptchaGenerator.php";
include_once "Configuration.php";


class CaptchaGeneratorTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException InvalidArgumentException
     */
    public function testOptionalCollaboration()
    {
        $generator = new CaptchaGenerator('nonConfigurationObject');
    }



    public function testInstantiation()
    {
        $this->assertInstanceOf('CaptchaGenerator', new CaptchaGenerator(new Configuration()));
        $this->assertInstanceOf('CaptchaGenerator', new CaptchaGenerator());

    }


    public function testGetConfiguration()
    {
        $configuration = new Configuration();

        $generator = new CaptchaGenerator($configuration);

        $this->assertEquals($configuration,$generator->getConfiguration());
    }

    public function testCodeGeneration()
    {
        $generatorWithoutInitialCode = new CaptchaGenerator(new Configuration());

        $this->assertEmpty($generatorWithoutInitialCode->getConfiguration()->asHash()['code']);

        $generatorWithoutInitialCode->generateCode();
        $this->assertNotEmpty($generatorWithoutInitialCode->getConfiguration()->asHash()['code']);
        $this->assertInternalType('string',$generatorWithoutInitialCode->getConfiguration()->asHash()['code']);

        $generatorWithInitialCode = new CaptchaGenerator(new Configuration(array('code' => '1234')));
        $generatorWithInitialCode->generateCode();
        $this->assertEquals('1234',$generatorWithInitialCode->getConfiguration()->asHash()['code']);


    }

}
