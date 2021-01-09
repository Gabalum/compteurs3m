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

    public function __construct($id, $name = '')
    {
        $this->id = $id;
        $this->name = $name;
        $this->archiveLink = 'http://data.montpellier3m.fr/sites/default/files/ressources/MMM_EcoCompt_'.$id.'_archive.json';
        $this->link = 'http://data.montpellier3m.fr/sites/default/files/ressources/MMM_EcoCompt_'.$id.'.json';
        $this->file = dirname(__DIR__).'/data/'.$id.'.json';
    }

    public function getName(bool $stripTags = false) : string
    {
        return ($stripTags ? strip_tags($this->name) : $this->name);
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
                    $year = Helper::extractDate($val->dateObserved, true);
                    $date = Helper::extractDate($val->dateObserved);
                    $cpt = $val->intensity;
                    // --- gestion de l'année courante
                    if($year == _YEAR_){
                        if($cpt >= $compteur['recordYear']){
                            $compteur['recordYear'] = $cpt;
                            $compteur['recordYearDate'] = $date;
                        }
                        $compteur['sumCurYear'] += $cpt;
                        $daysCurYear++;
                        $compteur['dataCurYear'][$date] = $cpt;
                        $compteur['dataCurYearDates'][] = $date;
                        $compteur['dataCurYearValues'][] = $cpt;
                    }
                    // --- gestion depuis le début
                    if($cpt >= $compteur['recordTotal']){
                        $compteur['recordTotal'] = $cpt;
                        $compteur['recordTotalDate'] = $date;
                    }
                    $compteur['sumTotal'] += $cpt;
                    $daysTotal++;
                    $compteur['dataTotal'][$date] = $cpt;
                    $compteur['dataTotalDates'][] = $date;
                    $compteur['dataTotalValues'][] = $cpt;
                }
                $compteur['lastDate'] = $date;
                $compteur['lastValue'] = $cpt;
                if(isset($val->location) && is_object($val->location) && isset($val->location->coordinates) && is_array($val->location->coordinates)){
                    $compteur['lat'] = $val->location->coordinates[1];
                    $compteur['lng'] = $val->location->coordinates[0];
                }
                $compteur['avgCurYear']  = ($daysCurYear > 0 ? intval($compteur['sumCurYear'] / $daysCurYear) : 0);
                $compteur['avgTotal']  = ($daysTotal > 0 ? intval($compteur['sumTotal'] / $daysTotal) : 0);
            }
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
        }
        else{
            if(isset($this->data[$item])){
                $retour = $this->data[$item];
            }
        }
        return $retour;
    }
}
