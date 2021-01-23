<?php
namespace App;

if(!defined('_YEAR_')){
    define('_YEAR_', date('Y'));
}
class DaysHoliday
{
    private static $_instance = null;

    private $link = null;
    private $archiveLink = null;
    private $file = null;
    private $data = null;

    private function __construct()
    {
        $this->file = dirname(__DIR__).'/data/feries.json';
        $this->link = 'https://calendrier.api.gouv.fr/jours-feries/metropole.json';
    }

    public static function getInstance()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new DaysHoliday();
        }
        return self::$_instance;
    }

    public function getData()
    {
        if($this->data == null){
            if(!file_exists($this->file)){
                $this->retrieveData();
            }elseif(date('Y', filemtime($this->file)) < date('Y')){ // une fois par an
                $this->retrieveData();
            }
            $this->data = json_decode(file_get_contents($this->file), true);
        }
        return $this->data;
    }

    public function retrieveData()
    {
        $values = [];
        $data = @file_get_contents($this->link);
        if(strlen($data) > 0){
            $data = @json_decode($data);
            if (json_last_error() === JSON_ERROR_NONE) {
                foreach($data as $k => $v){
                    $date = explode('-', $k);
                    if($date[0] >= 2020){
                        if(!isset($values[$date[0]])){
                            $values[$date[0]] = [];
                        }
                        $values[$date[0]][$k] = [
                            'date'  => $date[2].'-'.$date[1].'-'.$date[0],
                            'label' => $v,
                        ];
                    }
                }
            }
        }
        $file = fopen($this->file, 'w+');
        fwrite($file, json_encode($values, true));
        fclose($file);
    }
}
