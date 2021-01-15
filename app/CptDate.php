<?php
namespace App;

class CptDate extends \DateTime
{
    public function __construct($str)
    {
        $date = null;
        $x = explode('/', $str);
        if(isset($x[0])){
            $x = explode('T', $x[0]);
            if(isset($x[0])){
                $date = $x[0];
            }
        }
        parent::__construct($date);
    }
}
