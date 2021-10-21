<?php
namespace App;

class TotemAlbert extends TotemManager
{
    protected $startKeys    = 0;
    protected $whichOne     = 'toto';
    protected $name         = 'Albert 1<sup>er</sup>';
    protected $firstDay     = '12/03/2020';

    public static function getInstance()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
