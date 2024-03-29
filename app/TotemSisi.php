<?php
namespace App;

class TotemSisi extends TotemManager
{
    protected $startKeys    = 10;
    protected $whichOne     = 'sisi';
    protected $name         = 'Simone Veil';
    protected $firstDay     = '20/10/2021';

    public static function getInstance()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
