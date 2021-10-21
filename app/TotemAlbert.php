<?php
namespace App;

class TotemAlbert extends TotemManager
{
    protected $startKeys    = 0;
    protected $whichOne     = 'toto';

    public static function getInstance()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
