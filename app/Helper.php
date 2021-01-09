<?php
namespace App;

class Helper
{

    public static function extractDate($str, $onlyYear = false, $join = false)
    {
        $date = '';
        $x = explode('/', $str);
        if(isset($x[0])){
            $x = explode('T', $x[0]);
            if(isset($x[0])){
                $d = explode('-', $x[0]);
                if(count($d) == 3){
                    if($join){
                        $date = $d[0].$d[1].$d[2];
                    }elseif($onlyYear){
                        $date = $d[0];
                    }else{
                        $date = $d[2].'-'.$d[1].'-'.$d[0];
                    }
                }

            }
        }
        return $date;
    }

    public static function slugify($string, $delimiter = '-')
    {
        $string = strip_tags($string);
    	$oldLocale = setlocale(LC_ALL, '0');
    	setlocale(LC_ALL, 'fr_FR.UTF-8');
    	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
    	$clean = strtolower($clean);
    	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
    	$clean = trim($clean, $delimiter);
    	setlocale(LC_ALL, $oldLocale);
    	return $clean;
    }

    public static function noCache($file = '')
    {
        $retour = $file;
        $file = str_replace('./', dirname(__DIR__).'/www/', $file);
        if(file_exists($file)){
            $retour .= '?v='.filemtime($file);
        }
        return $retour;
    }
}
