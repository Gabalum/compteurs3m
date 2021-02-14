<?php
namespace App;

class AlbertDate extends \DateTime
{
    public function __construct($str, $str2 = null)
    {
        $date = null;
        $x = explode('/', $str);
        if(count($x) === 3){
            $date = $x[2].'-'.$x[1].'-'.$x[0];
            if(!is_null($str2)){
                $date .= ' '.$str2;
            }
        }
        parent::__construct($date);
    }
}
