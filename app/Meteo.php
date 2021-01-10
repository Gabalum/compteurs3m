<?php
namespace App;

use \DateTime;

/**
* @see https://public.opendatasoft.com/explore/dataset/donnees-synop-essentielles-omm/information/?flg=fr
* @see https://donneespubliques.meteofrance.fr/?fond=produit&id_produit=90&id_rubrique=32
*/
class Meteo
{
    private $data = null;
    private $weatherData = null;
    private $link = null;
    private $file = null;

    public function __construct($rows = 200)
    {
        $rows = intval($rows);
        $this->link = 'https://public.opendatasoft.com/api/records/1.0/search/?dataset=donnees-synop-essentielles-omm&q=montpellier&rows='.$rows.'&sort=date&facet=date&facet=nom&facet=temps_present&refine.numer_sta=07643';
        $this->file = dirname(__DIR__).'/data/meteo_'.$rows.'.json';
        $this->weatherFile = dirname(__DIR__).'/data/weather.json';
    }

    public function getData()
    {
        if($this->data == null){
            if(!file_exists($this->file)){
                $this->retrieveData();
            }elseif(date('Ymd', filemtime($this->file)) < date('Ymd')){ // si fichier de la veille
            //}elseif(time() - filemtime($this->file) > 86400){ // une fois par jour
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
        $items = ['rr3', 'rr6', 'rr12', 'rr24', 'ff', 'dd', 'cod_tend'];
        if(is_object($data) && isset($data->records) && is_array($data->records) && count($data->records) > 0){
            foreach($data->records as $record){
                $date = new DateTime($record->fields->date);
                $h = $date->format('H');
                if(in_array($h,$heures)) {
                    if(!isset($values[$date->format('Ymd')])){
                        $values[$date->format('Ymd')] = [
                            'date'          => $date->format('d-m-Y'),
                            '06'            => '',
                            '12'            => '',
                            '18'            => '',
                            '06_raw'        => '',
                            '12_raw'        => '',
                            '18_raw'        => '',
                            '06_rr3'        => '', // précipitations sous 3 heures
                            '06_rr6'        => '', // précipitations sous 6 heures
                            '06_rr12'       => '', // précipitations sous 12 heures
                            '06_rr24'       => '', // précipitations sous 24 heures
                            '06_ff'         => '', // force du vent
                            '06_dd'         => '', // force du vent
                            '06_cod_tend'   => '', // type tendance baro
                            '12_rr3'        => '', // précipitations sous 3 heures
                            '12_rr6'        => '', // précipitations sous 6 heures
                            '12_rr12'       => '', // précipitations sous 12 heures
                            '12_rr24'       => '', // précipitations sous 24 heures
                            '12_ff'         => '', // force du vent
                            '12_dd'         => '', // force du vent
                            '12_cod_tend'   => '', // type tendance baro
                            '18_rr3'        => '', // précipitations sous 3 heures
                            '18_rr6'        => '', // précipitations sous 6 heures
                            '18_rr12'       => '', // précipitations sous 12 heures
                            '18_rr24'       => '', // précipitations sous 24 heures
                            '18_ff'         => '', // force du vent
                            '18_dd'         => '', // force du vent
                            '18_cod_tend'   => '', // type tendance baro
                        ];
                    }
                    $values[$date->format('Ymd')][$h] = (strlen($record->fields->tc) > 0 ? $record->fields->tc.' °C' : '-');
                    $values[$date->format('Ymd')][$h.'_raw'] = (strlen($record->fields->tc) > 0 ? $record->fields->tc : '-');
                    foreach($items as $it){
                        $values[$date->format('Ymd')][$h.'_'.$it] =  (strlen($record->fields->$it) > 0 ? $record->fields->$it : '-');
                    }
                }
            }
            ksort($values);
        }
        $file = fopen($this->file, 'w+');
        fwrite($file, json_encode($values, true));
        fclose($file);
    }

    public function getWeather()
    {
        if($this->weatherData == null){
            if(!file_exists($this->weatherFile)){
                $this->retrieveWeather();
            }elseif(date('Ymd', filemtime($this->file)) < date('Ymd')){ // si fichier de la veille
            //}elseif(time() - filemtime($this->weatherFile) > 86400){ // une fois par jour
                $this->retrieveWeather();
            }
            $this->weatherData = json_decode(file_get_contents($this->weatherFile), true);
        }
        return $this->weatherData;
    }

    /**
    * @see https://openweathermap.org/weather-conditions#Weather-Condition-Codes-2
    */
    public function retrieveWeather()
    {
        $values = [];
        for($i = 5; $i > 0; $i--){
            $date = (new DateTime())->modify('-'.$i.' days');
            $tmp = dirname(__DIR__).'/data/weather/'.$date->format('Ymd').'.json';
            if(!file_exists($tmp)){
                $apiKey = $_ENV['WEATHER_KEY'];
                $cityId = $_ENV['WEATHER_CITYID'];
                $cityCountry = $_ENV['WEATHER_CITYCODE'];
                $lat = $_ENV['WEATHER_LAT'];
                $lon = $_ENV['WEATHER_LON'];
                $dt = $date->format('U');
                $url = 'https://api.openweathermap.org/data/2.5/onecall/timemachine?lat='.$lat.'&lon='.$lon.'&dt='.$dt.'&appid='.$apiKey;
                $data = file_get_contents($url);
                $handler = fopen($tmp, 'w+');
                fwrite($handler, $data);
                fclose($handler);
            }
        }
        $files = glob(dirname(__DIR__).'/data/weather/*.json');
        if(count($files) > 0){
            foreach($files as $file){
                if(filemtime($file) > time() - 86400*20){ // données de 20 jours seulement
                    $data = json_decode(file_get_contents($file));
                    if(is_object($data) && isset($data->hourly) && is_array($data->hourly) && count($data->hourly) > 0){
                        foreach($data->hourly as $item){
                            $date = (new DateTime())->setTimestamp($item->dt);
                            $heures = ['06', '12', '18'];
                            if(in_array($date->format('H'), $heures)){
                                if(!isset($values[$date->format('Ymd')])){
                                    $values[$date->format('Ymd')] = [
                                        '06' => [],
                                        '12' => [],
                                        '18' => [],
                                    ];
                                }
                                $values[$date->format('Ymd')][$date->format('H')] = [
                                    'code'      => $item->weather[0]->id,
                                    'title'     => $item->weather[0]->main,
                                    'desc'      => Helper::slugify($item->weather[0]->description),
                                    'icon'      => $item->weather[0]->icon,
                                    'iconurl'   => 'http://openweathermap.org/img/wn/'.$item->weather[0]->icon.'@2x.png',
                                    'iconurl2'  => 'http://openweathermap.org/img/wn/'.str_replace('n', 'd', $item->weather[0]->icon).'@2x.png',
                                    'wi-icon'   => $this->icoToWi($item->weather[0]->icon),
                                ];
                            }
                        }
                    }
                }
            }
        }
        ksort($values);
        $file = fopen($this->weatherFile, 'w+');
        fwrite($file, json_encode($values, true));
        fclose($file);
    }

    /**
    * @see https://erikflowers.github.io/weather-icons/
    **/
    private function icoToWi($icon)
    {
        $icon = str_replace('n', 'd', $icon);
        $table = [
            '01d'   => 'wi-day-sunny',
            '02d'   => 'wi-day-cloudy',
            '03d'   => 'wi-cloud',
            '04d'   => 'wi-cloudy',
            '09d'   => 'wi-showers',
            '10d'   => 'wi-day-showers',
            '11d'   => 'wi-storm-showers',
            '13d'   => 'wi-snowflake-cold',
            '50d'   => 'wi-smoke',
        ];
        return (isset($table[$icon]) ? $table[$icon] : '');
    }
}
