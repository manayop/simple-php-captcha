<?php


class UbicationHandler
{
    public function isActualScriptInDocumentPath()
    {
        return strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']);
    }

    public function obtainActualScriptPath()
    {
        return str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']);
    }

    public function obtainUbication($file,$path)
    {
        return substr($file, strlen( realpath($path)));
    }

    public function obtainDocumentRoot()
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }



}