<?php

include_once 'NativeSession.php';


class SessionTest extends PHPUnit_Framework_TestCase
{
    public function testInstantiation()
    {
        $session = new NativeSession();
    }

    public function testImplementation()
    {
        $session = new NativeSession();

        $this->assertInstanceOf('Session',$session);
    }

    public function testSerializedSetAndGetWithPrefix()
    {
        $session = new NativeSession();

        $before = array(array(1,2,3),array(4,5,6));

        $session->serializedSetWithPrefix('prefix','variable',$before);
        $after = $session->serializedGetWithPrefix('prefix','variable');
        $this->assertEquals($before,$after);
    }

    public function testSetAndGet()
    {
        $session = new NativeSession();

        $before = 'abc';

        $session->set('variable',$before);
        $after = $session->get('variable');
        $this->assertEquals($before,$after);
    }

    public function testUnset()
    {
        $session = new NativeSession();

        $before = 'abc';

        $session->serializedSetWithPrefix('prefix','test',$before);

        $session->delete('prefix');

        $after = $session->serializedGetWithPrefix('prefix','test');

        $this->assertNull($after);

    }

    public function testClean()
    {
        $session = new NativeSession();

        $var1 = 'abc';
        $var2 = 'cde';

        $session->serializedSetWithPrefix('prefix','var1',$var1);
        $session->serializedSetWithPrefix('prefix','var2',$var2);

        $session->clean();

        $this->assertNull($session->serializedGetWithPrefix('prefix','var1'));
        $this->assertNull($session->serializedGetWithPrefix('prefix','var2'));

    }

}
