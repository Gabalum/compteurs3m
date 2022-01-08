<?php
namespace App;

class Helper
{

    /**
    * @deprecated
    */
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

    /**
    * @deprecated
    */
    public static function extractMonth($str)
    {
        $month = '';
        $x = explode('/', $str);
        if(isset($x[0])){
            $x = explode('T', $x[0]);
            if(isset($x[0])){
                $d = explode('-', $x[0]);
                if(count($d) == 3){
                    $month = $d[1];
                }
            }
        }
        return $month;
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

    public static function frenchMonth($num = 0, $prefixed = true)
    {
        $retour = '';
        $num = intval($num);
        $mois = [
            1 => 'janvier',
            2 => 'février',
            3 => 'mars',
            4 => "avril",
            5 => 'mai',
            6 => 'juin',
            7 => 'juillet',
            8 => "août",
            9 => 'septembre',
            10 => "octobre",
            11 => "novembre",
            12 => "décembre",
        ];
        if(isset($mois[$num])){
            if($prefixed){
                if(in_array($num, [4, 8, 10])){
                    $retour = "d'";
                }else{
                    $retour = "de ";
                }
            }
            $retour .= $mois[$num];
        }
        return $retour;
    }

    public static function frenchMonthWithoutPrefix($num = 0)
    {
        return self::frenchMonth($num, false);
    }

    public static function frenchDayOfTheWeek($dow)
    {
        $retour = '';
        $days = [
            1 => 'lundi',
            2 => 'mardi',
            3 => 'mercredi',
            4 => 'jeudi',
            5 => 'vendredi',
            6 => 'samedi',
            7 => 'dimanche',
        ];
        if(isset($days[$dow])){
            $retour = $days[$dow];
        }
        return $retour;
    }

    public static function colorGenerator()
    {
        $retour = '#';
        for($i = 1; $i <= 3 ; $i++){
            $retour .= str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
        }
        return $retour;
    }

    public static function isFerie($date)
    {
        $feries = DaysHoliday::getInstance()->getData();
        return (isset($feries[$date->format('Y')]) && is_array($feries[$date->format('Y')]) && isset($feries[$date->format('Y')][$date->format('Y-m-d')]));
    }

    public static function nf($value)
    {
        return number_format($value, 0, ',', '&thinsp;');
    }
}
