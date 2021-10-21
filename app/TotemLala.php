<?php
namespace App;

class TotemLala extends TotemManager
{
    protected $startKeys    = 5;
    protected $whichOne     = 'lala';
    protected $name         = 'Lavérune';
    protected $firstDay     = '29/09/2021';

    public static function getInstance()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
