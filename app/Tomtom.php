<?php
namespace App;

if(!defined('_YEAR_')){
    define('_YEAR_', date('Y'));
}
class Tomtom
{
    private static $_instance = null;

    private $link = null;
    private $archiveLink = null;
    private $file = null;
    private $data = null;

    private function __construct()
    {
        $this->file = dirname(__DIR__).'/data/tomtom.json';
        $this->link = 'https://api.midway.tomtom.com/ranking/dailyStats/FRA_montpellier';
    }

    public static function getInstance()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new Tomtom();
        }
        return self::$_instance;
    }

    public function getData()
    {
        if($this->data == null){
            if(!file_exists($this->file)){
                $this->retrieveData();
            }elseif(date('Ymd', filemtime($this->file)) < date('Ymd')){ // si fichier de la veille
                $this->retrieveData();
            }
            $this->data = json_decode(file_get_contents($this->file), true);
        }
        return $this->data;
    }

    public function retrieveData()
    {
        $values = [];
        $data = @gzdecode(@file_get_contents($this->link));
        if(strlen($data) > 0){
            $data = @json_decode($data);
            if (json_last_error() === JSON_ERROR_NONE) {
                foreach($data as $k => $v){
                    $date = explode('-', $v->date);
                    if(intval($date[0]) == _YEAR_){
                        $values[] = $v;
                    }
                }
            }
        }
        $file = fopen($this->file, 'w+');
        fwrite($file, json_encode($values, true));
        fclose($file);
    }
}
