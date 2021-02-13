<?php
namespace App;

class Albert
{
    private static $_instance = null;
    private $data = null;
    private $link = null;
    private $file = null;

    private function __construct()
    {
        //$this->link = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQVtdpXMHB4g9h75a0jw8CsrqSuQmP5eMIB2adpKR5hkRggwMwzFy5kB-AIThodhVHNLxlZYm8fuoWj/pub?gid=59478853&single=true&output=csv';
        $this->link = '../data/test2.csv';
        $this->file = dirname(__DIR__).'/data/albert.json';
    }

    public static function getInstance()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getData()
    {
        if($this->data == null){
            if(!file_exists($this->file)){
                $this->retrieveData();
            }elseif(time() - filemtime($this->file) > 600) { // toutes les 10 minutes
                $this->retrieveData();
            }
            $this->data = json_decode(file_get_contents($this->file), true);
        }
        return $this->data;
    }

    public function retrieveData()
    {
        $values = [];
        $handle = fopen($this->link, 'r');
        if($handle){
            $first = true;
            $prevDate = 0;
            $prevTotal = 0;
            $prevYear = 0;
            while($item = fgetcsv($handle)){
                if($first){
                    $first = false;
                    continue;
                }
                if(isset($item[1]) && strlen($item[1]) === 10){
                    if(isset($item[0]) && ($item[0] == '24/06/2020 19:18:17' || $item[0] == '16/06/2020 22:05:56,15/06/2020')){
                        continue;
                    }
                    $date = new AlbertDate($item[1]);
                    $y = $date->format('Y');
                    $d = $date->format('Ymd');
                    if($prevYear != $y){
                        $prevDate = 0;
                        $prevTotal = 0;
                        $prevYear = $y;
                    }
                    if(!isset($values[$y])){
                        $values[$y] = [];
                    }
                    if(!isset($values[$y][$d])){
                        $values[$y][$d] = [];
                    }
                    $instantTotal = (isset($item[3]) ? intval($item[3]) : 0);
                    $instant = (isset($item[4]) ? intval($item[4]) : 0);
                    $values[$y][$d][] = [
                        'horodateur'    => (isset($item[0]) ? $item[0] : ''),
                        'jour'          => Helper::frenchDayOfTheWeek($date->format('N')),
                        'date'          => $date->format('d-m-Y'),
                        'dateOrig'      => $item[1],
                        'heure'         => (isset($item[2]) ? (string)$item[2] : ''),
                        'total'         => $instantTotal,
                        'instant'       => $instant,
                        'media'         => (isset($item[5]) ? $item[5] : ''),
                        'comment'       => (isset($item[6]) ? $item[6] : ''),
                        'date2'         => (isset($item[7]) ? $item[7] : ''),
                        'xxx'           => (isset($item[8]) ? $item[8] : ''),
                        'yyy'           => (isset($item[9]) ? $item[9] : ''),
                        'totalJour'     => 0,
                        'isFerie'       => Helper::isFerie($date),
                    ];
                    if($prevDate != $d){
                        if(isset($values[$y][$prevDate])){
                            $theTotal = $instantTotal - $instant - $prevTotal;
                            $prevTotal += $theTotal;
                            foreach($values[$y][$prevDate] as $kk => $prevVal){
                                $values[$y][$prevDate][$kk]['totalJour'] = $theTotal;
                            }
                        }
                        $prevDate = $d;
                    }
                }
            }
        }
        //var_dump($values[2020]);die();
        $file = fopen($this->file, 'w+');
        fwrite($file, json_encode($values, true));
        fclose($file);
    }
}
