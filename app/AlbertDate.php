<?php
namespace App;

class AlbertDate extends \DateTime
{
    public function __construct($str)
    {
        $date = null;
        $x = explode('/', $str);
        if(count($x) === 3){
            $date = $x[2].'-'.$x[1].'-'.$x[0];
        }
        parent::__construct($date);
    }
}
