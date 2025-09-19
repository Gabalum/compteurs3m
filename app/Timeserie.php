<?php
namespace App;

if(!defined('_YEAR_')){
    define('_YEAR_', date('Y'));
}
class Timeserie
{
    private $id;
    private $ecocounterId;
    private $link;
    private $data = [
        'raw'       => [],
        'days'      => [],
        'monthes'   => [],
        'record'       => [
            'date'  => null,
            'value' => null,
        ],
        'total'     => [
            'tot'   => [],
            'avg'   => [],
            'med'   => [],
        ],
    ];

    public function __construct($id, $year = null)
    {
        $this->id = $id;
        $this->ecocounterId = 'urn:ngsi-ld:EcoCounter:'.$id;
        $this->file = dirname(__DIR__).'/data/timeseries/'.$id.'.json';
        if(is_null($year)) {
            $year = _YEAR_;
        }
        $dateStart  = $year.'-01-01T00:00:00';
        $dateEnd    = date('Y-m-d').'T23:59:59';
        $this->link = 'https://portail-api-data.montpellier3m.fr/ecocounter_timeseries/'.urlencode($this->ecocounterId).'/attrs/intensity?fromDate='.urlencode($dateStart).'&toDate='.urlencode($dateEnd);
        $oneDay = [];
        $med = [];
        for($i = 0 ; $i < 24 ; $i++){
            $oneDay[$i] = 0;
            $med[$i] = null;
        }
        for($i = 1 ; $i < 13 ; $i++){
            if($i < 8){
                $this->data['days'][$i] = [
                    'raw'   => [],
                    'tot'   => $oneDay,
                    'num'   => $oneDay,
                    'avg'   => $oneDay,
                    'min'   => $oneDay,
                    'max'   => $oneDay,
                    'med'   => $oneDay,
                    'medD'  => $med,
                ];
            }
            $this->data['monthes'][$i] = [
                'raw'   => [],
                'tot'   => $oneDay,
                'num'   => $oneDay,
                'avg'   => $oneDay,
                'min'   => $oneDay,
                'max'   => $oneDay,
                'med'   => $oneDay,
                'medD'  => $med,
            ];
        }
    }

    public function process()
    {
        $max = 0;
        $data = json_decode(@file_get_contents($this->link), true);
        if(is_array($data) && isset($data['index']) && isset($data['values'])){
            $this->data['raw'] = [];
            foreach($data['index'] as $k => $v){
                $val = (isset($data['values'][$k]) ? (int) $data['values'][$k] : 0);
                $date = new \DateTime($v);
                if($val > $max) {
                    $max = $val;
                    $this->data['record']['date'] = 'le '.$date->format('d-m-Y').' de '.(int)$date->format('H').'h à '.((int)$date->format('H')+1).'h';
                    $this->data['record']['value'] = $val;
                }
                $this->data['raw'][$v] = $val;
                $hour = (int) $date->format('H');
                foreach(['days', 'monthes'] as $type){
                    $index = ($type == 'days' ? (int) $date->format('N') : (int) $date->format('m'));
                    if(!isset($this->data[$type][$index]['raw'][$hour])){
                        $this->data[$type][$index]['raw'][$hour] = [];
                    }
                    $this->data[$type][$index]['raw'][$hour][] = $val;
                    $this->data[$type][$index]['tot'][$hour] += $val;
                    $this->data[$type][$index]['num'][$hour]++;
                    if(is_null($this->data[$type][$index]['medD'][$hour])){
                        $this->data[$type][$index]['medD'][$hour] = $val;
                    }else{
                        $this->data[$type][$index]['medD'][$hour] .= ','.$val;
                    }
                }
            }
            foreach(['days', 'monthes'] as $type){
                for($day = 1 ; $day < 8 ; $day++){
                    foreach($this->data[$type][$day]['tot'] as $hour => $val){
                        $count = max(1, $this->data[$type][$day]['num'][$hour]);
                        $this->data[$type][$day]['avg'][$hour] = round($val / $count, 2);
                        if(!is_null($this->data[$type][$day]['medD'][$hour])){
                            $medVals = explode(',',$this->data[$type][$day]['medD'][$hour]);
                            sort($medVals);
                            $this->data[$type][$day]['med'][$hour] = $medVals[floor(($count-1)/2)];
                            $this->data[$type][$day]['min'][$hour] = min($medVals);
                            $this->data[$type][$day]['max'][$hour] = max($medVals);
                        }
                    }
                }
            }
        }
        $file = fopen($this->file, 'w+');
        fwrite($file, json_encode($this->data, true));
        fclose($file);
    }

    public function getData()
    {
        return json_decode(@file_get_contents($this->file), true);
    }
}
