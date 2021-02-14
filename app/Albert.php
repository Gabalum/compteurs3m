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
        $this->link = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQVtdpXMHB4g9h75a0jw8CsrqSuQmP5eMIB2adpKR5hkRggwMwzFy5kB-AIThodhVHNLxlZYm8fuoWj/pub?gid=59478853&single=true&output=csv';
        //$this->link = '../data/test2.csv';
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
        $data = [];
        $prevDate = 0;
        $prevTotal = 0;
        $prevYear = 0;
        $handle = fopen($this->link, 'r');
        if($handle){
            $first = true;
            while($item = fgetcsv($handle)){
                if($first){
                    $first = false;
                    continue;
                }
                if(isset($item[1]) && strlen($item[1]) === 10 && isset($item[2]) && strlen($item[2]) == 8){
                    $date = new AlbertDate($item[1], $item[2]);
                    $item['date'] = $date;
                    $data[$date->format('YmdHis')] = $item;
                }
            }
        }
        fclose($handle);
        ksort($data);
        if(count($data) > 0){
            foreach($data as $key => $item){
                $date = $item['date'];
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
                    'heure'         => $date->format('H:i:s'),
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
        $file = fopen($this->file, 'w+');
        fwrite($file, json_encode($values, true));
        fclose($file);
    }

    public function getQuartileMonthes()
    {
        $monthes = [];
        if(count($this->data) > 0){
            foreach($this->data as $year => $values){
                if(!isset($monthes[$year])){
                    $monthes[$year] = [];
                }
                if(count($values) > 0){
                    foreach($values as $dates){
                        if(count($dates) > 0){
                            $prev = 0;
                            foreach($dates as $k => $item){
                                $date = new AlbertDate($item['dateOrig']);
                                $month = $date->format('m');
                                if(!isset($monthes[$year][$month])){
                                    $monthes[$year][$month] = [
                                        '10'    => [
                                            'cpt'   => 0,
                                            'total' => 0,
                                            'avg'   => 0,
                                        ],
                                        '14'    => [
                                            'cpt'   => 0,
                                            'total' => 0,
                                            'avg'   => 0,
                                        ],
                                        '18'    => [
                                            'cpt'   => 0,
                                            'total' => 0,
                                            'avg'   => 0,
                                        ],
                                        'total'    => [
                                            'cpt'   => 0,
                                            'total' => 0,
                                            'avg'   => 0,
                                        ],
                                    ];
                                }
                                $h = str_replace(':', '', $item['heure']);
                                $key = null;
                                if($h <= 100000){
                                    $key = '10';
                                }elseif($h <= 140000){
                                    $key = '14';
                                }elseif($h <= 180000){
                                    $key = '18';
                                }
                                if(!is_null($key)){
                                    $monthes[$year][$month][$key]['cpt']++;
                                    $monthes[$year][$month][$key]['total'] += $item['instant'];
                                }
                                if($prev != $date->format('Ymd')){
                                    $monthes[$year][$month]['total']['cpt']++;
                                    $monthes[$year][$month]['total']['total'] += $item['totalJour'];
                                    $prev = $date->format('Ymd');
                                }
                            }
                        }
                    }
                }
            }
            foreach($monthes as $year => $mm){
                foreach($mm as $month => $values){
                    foreach($values as $key => $val){
                        $monthes[$year][$month][$key]['avg'] = intval($val["total"] / $val['cpt']);
                    }
                    $monthes[$year][$month]['total']['avg'] -= $monthes[$year][$month][18]['avg'];
                    $monthes[$year][$month]['name'] = Helper::frenchMonth($month, false);
                }
            }
        }
        return $monthes;
    }
}
