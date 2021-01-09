<?php
namespace App;

use \DateTime;

class Meteo
{
    private $data = null;
    private $link = 'https://public.opendatasoft.com/api/records/1.0/search/?dataset=donnees-synop-essentielles-omm&q=montpellier&rows=2000&sort=date&facet=date&facet=nom&facet=temps_present&refine.numer_sta=07643';
    private $file = null;

    public function __construct()
    {
        $this->file = dirname(__DIR__).'/data/meteo.json';
    }

    public function getData()
    {
        if($this->data == null){
            if(!file_exists($this->file)){
                $this->retrieveData();
            }elseif(time() - filemtime($this->file) > 86400){ // une fois par jour
                $this->retrieveData();
            }
            $this->data = json_decode(file_get_contents($this->file), true);
        }
        return $this->data;
    }

    public function retrieveData()
    {
        $data = @json_decode(@file_get_contents($this->link));
        $values = [];
        $heures = ['06', '12', '18'];
        if(is_object($data) && isset($data->records) && is_array($data->records) && count($data->records) > 0){
            foreach($data->records as $record){
                $date = new DateTime($record->fields->date);
                if(in_array($date->format('H'),$heures)) {
                    if(!isset($values[$date->format('Ymd')])){
                        $values[$date->format('Ymd')] = [
                            'date'  => $date->format('d-m-Y'),
                            '06'  => '',
                            '12'  => '',
                            '18'  => '',
                        ];
                    }
                    $values[$date->format('Ymd')][$date->format('H')] = (strlen($record->fields->tc) > 0 ? $record->fields->tc.' °C' : '-');
                }
            }
            ksort($values);
        }
        $file = fopen($this->file, 'w+');
        fwrite($file, json_encode($values, true));
        fclose($file);
    }
}
