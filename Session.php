<?php


interface Session
{

    public function start();

    public function clean();

    public function serializedSetWithPrefix($prefix,$name,$value);

    public function serializedGetWithPrefix($prefix,$name);

    public function set($name,$value);

    public function get($name);

    public function delete($name);
}