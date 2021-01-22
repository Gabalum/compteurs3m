<?php
namespace App;

if(!defined('_YEAR_')){
    define('_YEAR_', date('Y'));
}
class Compteur
{
    private $id;
    private $name = '';
    private $link = null;
    private $archiveLink = null;
    private $file = null;
    private $data = null;
    private $address = '';

    public function __construct($id, $name = '', $address = '', $color = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->archiveLink = 'http://data.montpellier3m.fr/sites/default/files/ressources/MMM_EcoCompt_'.$id.'_archive.json';
        $this->link = 'http://data.montpellier3m.fr/sites/default/files/ressources/MMM_EcoCompt_'.$id.'.json';
        $this->file = dirname(__DIR__).'/data/'.$id.'.json';
        $this->address = $address;
        if(strlen($color) === 0){
            $this->color = Helper::colorGenerator();
        }else{
            $this->color = $color;
        }
    }

    public function getName(bool $stripTags = false) : string
    {
        return ($stripTags ? strip_tags($this->name) : $this->name);
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getAddress() : string
    {
        return $this->address;
    }

    public function getColor() : string
    {
        return $this->color;
    }

    public function setAddress(string $address = '')
    {
        return $this->address = $address;
    }

    public function getData()
    {
        if($this->data == null){
            if(!file_exists($this->file)){
                $this->retrieveData();
            }elseif(time() - filemtime($this->file) > 900){ // toutes les 15 minutes
                $this->retrieveData();
            }
            $this->data = json_decode(file_get_contents($this->file), true);
        }
        return $this->data;
    }

    private function retrieveData()
    {
        $compteur = [
            'labelHTML'         => $this->getName(),
            'label'             => $this->getName(true),
            'slug'              => Helper::slugify($this->getName(true), '-'),
            'id'                => $this->id,
            'lat'               => '',
            'lng'               => '',
            'dataCurYear'       => [],
            'dataCurYearDates'  => [],
            'dataCurYearValues' => [],
            'dataTotal'         => [],
            'dataTotalDates'    => [],
            'dataTotalValues'   => [],
            'sumCurYear'        => 0,
            'avgCurYear'        => 0,
            'sumTotal'          => 0,
            'avgTotal'          => 0,
            'recordYear'        => 0,
            'recordTotal'       => 0,
            'recordYearDate'    => '',
            'recordTotalDate'   => '',
            'lastValue'         => 0,
            'lastDate'          => '',
            'monthes'           => [],
            'days'              => [],
            'days-by-year'      => [],
            'weeks'             => [],
        ];
        $daysTotal = 0;
        $daysCurYear = 0;
        $data = @file_get_contents($this->archiveLink);
        $data = trim($data);
        $data = str_replace('{"int', ',{"int', $data);
        $data = '['.substr($data, 1).']';
        if($data && strlen($data) > 0){
            $values = json_decode($data);
            if(count($values) > 0){
                foreach($values as $val){
                    $date = new CptDate($val->dateObserved);
                    $year = $date->format('Y');
                    $month = $date->format('m');
                    $fDate = $date->format('d-m-Y');
                    $dayOfTheWeek = $date->format('N');
                    $cpt = $val->intensity;
                    // --- gestion de l'année courante
                    if($year == _YEAR_){
                        $week = $date->format('W');
                        if($cpt >= $compteur['recordYear']){
                            $compteur['recordYear'] = $cpt;
                            $compteur['recordYearDate'] = $fDate;
                        }
                        $compteur['sumCurYear'] += $cpt;
                        $daysCurYear++;
                        $compteur['dataCurYear'][$fDate] = $cpt;
                        $compteur['dataCurYearDates'][] = $fDate;
                        $compteur['dataCurYearValues'][] = $cpt;
                        // --- gestion par mois
                        if(!isset($compteur["monthes"][$month])){
                            $compteur['monthes'][$month] = [
                                'value'    => 0,
                                'date'      => null,
                                'cpt'       => 0,
                                'sum'       => 0,
                                'avg'       => 0,
                            ];
                        }
                        if($cpt > $compteur['monthes'][$month]['value']){
                            $compteur['monthes'][$month]['value'] = $cpt;
                            $compteur['monthes'][$month]['date'] = $fDate;
                        }
                        $compteur['monthes'][$month]['cpt']++;
                        $compteur['monthes'][$month]['sum'] += $cpt;
                        // --- gestion des semaines
                        if(!isset($compteur['weeks'][$week])){
                            $compteur['weeks'][$week] = [
                                'value' => 0,
                                'date'  => null,
                                'cpt'   => 0,
                                'sum'   => 0,
                                'avg'   => 0,
                            ];
                        }
                        if($cpt > $compteur['weeks'][$week]['value']){
                            $compteur['weeks'][$week]['value'] = $cpt;
                            $compteur['weeks'][$week]['date'] = $fDate;
                        }
                        $compteur['weeks'][$week]['cpt']++;
                        $compteur['weeks'][$week]['sum'] += $cpt;
                    }
                    // --- gestion du jour de la semaine par années
                    if(!isset($compteur['days-by-year'][$year])){
                        $compteur['days-by-year'][$year] = [];
                    }
                    if(!isset($compteur['days-by-year'][$year][$dayOfTheWeek])){
                        $compteur['days-by-year'][$year][$dayOfTheWeek] = [
                            'value' => 0,
                            'date'  => null,
                            'cpt'   => 0,
                            'sum'   => 0,
                            'avg'   => 0,
                        ];
                    }
                    if($cpt > $compteur['days-by-year'][$year][$dayOfTheWeek]['value']){
                        $compteur['days-by-year'][$year][$dayOfTheWeek]['value'] = $cpt;
                        $compteur['days-by-year'][$year][$dayOfTheWeek]['date'] = $fDate;
                    }
                    $compteur['days-by-year'][$year][$dayOfTheWeek]['cpt']++;
                    $compteur['days-by-year'][$year][$dayOfTheWeek]['sum'] += $cpt;
                    // --- gestion du jour de la semaine
                    if(!isset($compteur['days'][$dayOfTheWeek])){
                        $compteur['days'][$dayOfTheWeek] = [
                            'value' => 0,
                            'date'  => null,
                            'cpt'   => 0,
                            'sum'   => 0,
                            'avg'   => 0,
                        ];
                    }
                    if($cpt > $compteur['days'][$dayOfTheWeek]['value']){
                        $compteur['days'][$dayOfTheWeek]['value'] = $cpt;
                        $compteur['days'][$dayOfTheWeek]['date'] = $fDate;
                    }
                    $compteur['days'][$dayOfTheWeek]['cpt']++;
                    $compteur['days'][$dayOfTheWeek]['sum'] += $cpt;
                    // --- gestion depuis le début
                    if($cpt >= $compteur['recordTotal']){
                        $compteur['recordTotal'] = $cpt;
                        $compteur['recordTotalDate'] = $fDate;
                    }
                    $compteur['sumTotal'] += $cpt;
                    $daysTotal++;
                    $compteur['dataTotal'][$fDate] = $cpt;
                    $compteur['dataTotalDates'][$date->format('Ymd')] = $fDate;
                    $compteur['dataTotalValues'][$date->format('Ymd')] = $cpt;
                }
                $compteur['lastDate'] = $fDate;
                $compteur['lastValue'] = $cpt;
                if(isset($val->location) && is_object($val->location) && isset($val->location->coordinates) && is_array($val->location->coordinates)){
                    $compteur['lat'] = $val->location->coordinates[1];
                    $compteur['lng'] = $val->location->coordinates[0];
                }
                $compteur['avgCurYear']  = ($daysCurYear > 0 ? intval($compteur['sumCurYear'] / $daysCurYear) : 0);
                $compteur['avgTotal']  = ($daysTotal > 0 ? intval($compteur['sumTotal'] / $daysTotal) : 0);
            }
        }
        ksort($compteur['dataTotalDates']);
        ksort($compteur['dataTotalValues']);
        if(count($compteur['monthes']) > 0){
            foreach($compteur['monthes'] as $month => $val){
                if($val['cpt'] > 0){
                    $compteur['monthes'][$month]['avg'] = intval($val['sum'] / $val['cpt']);
                }
            }
        }
        if(count($compteur['days']) > 0){
            foreach($compteur['days'] as $dow => $val){
                if($val['cpt'] > 0){
                    $compteur['days'][$dow]['avg'] = intval($val['sum'] / $val['cpt']);
                }
            }
            ksort($compteur['days']);
        }
        if(count($compteur['days-by-year']) > 0){
            foreach($compteur['days-by-year'] as $year => $cptY){
                if(count($cptY) > 0){
                    foreach($cptY as $dow => $val){
                        if($val['cpt'] > 0){
                            $compteur['days-by-year'][$year][$dow]['avg'] = intval($val['sum'] / $val['cpt']);
                        }
                    }
                    ksort($compteur['days-by-year'][$year]);
                }
            }
        }
        if(count($compteur['weeks']) > 0){
            if(count($compteur['weeks']) < 50){
                if(isset($compteur['weeks'][52])){
                    unset($compteur['weeks'][52]);
                }
                if(isset($compteur['weeks'][53])){
                    unset($compteur['weeks'][53]);
                }
            }
            foreach($compteur['weeks'] as $week => $val){
                if($val['cpt'] > 0){
                    $compteur['weeks'][$week]['avg'] = intval($val['sum'] / $val['cpt']);
                }
            }
            ksort($compteur['weeks']);
        }
        $file = fopen($this->file, 'w+');
        fwrite($file, json_encode($compteur, true));
        fclose($file);
    }

    private function getChartDates(int $days = 0) : array
    {
        $retour = [];
        $dateLimit = new \DateTime();
        $dateLimit->modify('-'.$days.' days');
        $dateLimit = $dateLimit->format('Ymd');
        $dates = $this->get('dataTotalDates');
        if(is_array($dates) && count($dates) > 0){
            foreach($dates as $date){
                $exp = explode('-', $date);
                if(count($exp) == 3){
                    if($exp[2].$exp[1].$exp[0] > $dateLimit){
                        $retour[] = $date;
                    }
                }
            }
        }
        return $retour;
    }

    private function getChartData(int $days = 0) : array
    {
        $retour = [];
        $dateLimit = new \DateTime();
        $dateLimit->modify('-'.$days.' days');
        $dateLimit = $dateLimit->format('Ymd');
        $dates = $this->get('dataTotal');
        if(is_array($dates) && count($dates) > 0){
            foreach($dates as $date => $value){
                $exp = explode('-', $date);
                if(count($exp) == 3){
                    if($exp[2].$exp[1].$exp[0] > $dateLimit){
                        $retour[] = $value;
                    }
                }
            }
        }
        return $retour;
    }

    private function monthRecord()
    {
        $month = date('m');
        if(intval($month) == 1){
            return null;
        }
        $retour = null;
        $records = $this->get('monthes');
        if(isset($records[$month])){
            $retour = $records[$month];
        }
        return $retour;
    }

    public function getSumStack()
    {
        $retour = [];
        $data = [];
        $sum = 0;
        $data = $this->get('dataCurYear');
        if(count($data) > 0){
            foreach($data as $day => $value){
                $sum += $value;
                $retour[$day] = $sum;
            }
        }
        return $retour;
    }

    public function getWeekWeekend($year = null, $item = 'avg', $raw = false)
    {
        $retour = [
            'week'      => 0,
            'weekend'   => 0,
        ];
        if(!in_array($item, ['sum', 'avg', 'value'])){
            return $retour;
        }
        $data = [];
        if(is_null($year)){
            $data = $this->get('days');
        }else{
            $tmpData = $this->get('days-by-year');
            if($tmpData[$year]){
                $data = $tmpData[$year];
            }
        }
        if(count($data) > 0){
            foreach($data as $day => $value){
                $type = ($day < 6 ? 'week' : 'weekend');
                $retour[$type] += $value[$item];
            }
            $retour['week'] = ($retour['week'] / 5);
            $retour['weekend'] = ($retour['weekend'] / 2);
            if(!$raw){
                $retour['week'] = intval($retour['week']);
                $retour['weekend'] = intval($retour['weekend']);
            }
        }
        return $retour;
    }

    public function get($item, $days = 14)
    {
        if(is_null($this->data)){
            $this->getData();
        }
        $retour = null;
        if($item == 'chartDates'){
            $retour = $this->getChartDates($days);
        }elseif($item == 'chartData'){
            $retour = $this->getChartData($days);
        }elseif($item == 'monthRecord'){
            $retour = $this->monthRecord();
        }elseif($item == 'id'){
            $retour = $this->getId();
        }elseif($item == 'address'){
            $retour = $this->getAddress();
        }elseif($item == 'color'){
            $retour = $this->getColor();
        }else{
            if(isset($this->data[$item])){
                $retour = $this->data[$item];
            }
        }
        return $retour;
    }
}
