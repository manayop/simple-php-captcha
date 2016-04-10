<?php

include_once "LibraryNotExistsException.php";

class LibraryTest
{
    public function functionTest($functionName)
    {
        if( !function_exists($functionName) ) {
            throw new LibraryNotExistsException('Required GD library is missing');
        }
    }

}