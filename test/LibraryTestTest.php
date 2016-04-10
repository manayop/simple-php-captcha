<?php

include_once "LibraryTest.php";

class LibraryTestTest extends PHPUnit_Framework_TestCase
{

    /**
     * @expectedException LibraryNotExistsException
     */
    public function testInvalidLibrary()
    {
        $library = new LibraryTest();
        $library->functionTest('abc');

    }
}
