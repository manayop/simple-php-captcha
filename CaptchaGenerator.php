<?php


class CaptchaGenerator
{

    private $configuration;


    public function __construct($configuration = null)
    {
        if (null === $configuration){
            $configuration = new Configuration();
        }
        $this->testConfiguration($configuration);
        $this->configuration = $configuration;
    }

    private function testConfiguration($configuration)
    {
        if (!$configuration instanceof Configuration) throw new InvalidArgumentException();
    }

    public function generateCode()
    {
        if( empty($this->configuration->obtainValue('code'))) {
            $code = '';
            $length = mt_rand($this->configuration->obtainValue('min_length'), $this->configuration->obtainValue('max_length'));
            $characters = $this->configuration->obtainValue('characters');
            while( strlen($code) < $length  ){
                $code .= substr($characters, mt_rand() % (strlen($characters)), 1);
            }
            $this->configuration->setValue('code',$code);
        }
    }


    public function generateHTMLImageSource($ubication)
    {
        $image_src = $ubication .  '?_CAPTCHA&amp;t=' . urlencode(microtime());
        $image_src = '/' . ltrim(preg_replace('/\\\\/', '/', $image_src), '/');

        return $image_src;
    }


    public function getConfiguration()
    {
        return $this->configuration;
    }



}