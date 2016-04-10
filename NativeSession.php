<?php

include_once 'Session.php';

class NativeSession implements Session
{
    public function start()
    {
        session_start();
    }

    public function clean()
    {
        $_SESSION = array();
    }


    public function serializedSetWithPrefix($prefix,$name,$value)
    {
        $_SESSION[$prefix][$name] = serialize($value);
    }

    public function serializedGetWithPrefix($prefix,$name)
    {
        $result = null;
        if (null !== $_SESSION[$prefix][$name]){
            $result = unserialize($_SESSION[$prefix][$name]);
        }
        return $result;
    }

    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function get($name)
    {
        return $_SESSION[$name];
    }


    public function delete($name)
    {
        unset($_SESSION[$name]);
    }


}