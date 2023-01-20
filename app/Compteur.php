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
    private $totem = false;
    private $commune = '';
    private $address = '';
    private $fix = [];

    public function __construct($id, $name = '', $address = '', $color = '', bool $totem = false, string $commune = '', array $fix = [])
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
        $this->totem = $totem;
        $this->commune = $commune;
        if(isset($fix[$id])){
            $this->fix = $fix[$id];
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

    public function isTotem()
    {
        return $this->totem;
    }

    public function getCommune()
    {
        return $this->commune;
    }

    public function orderByDO($a, $b)
    {
        return $a->dateObserved > $b->dateObserved;
    }

    public function getData()
    {
        if($this->data == null){
            if(isset($_GET['reload']) || !file_exists($this->file)){
                $this->retrieveData();
            }elseif((date('dmY', filemtime($this->file)) !== date('dmY') || date('Hi', filemtime($this->file)) < 1000) && time() - filemtime($this->file) > 900){ // Si le fichier date de la veille ou d'avant 10, on regarde toutes les 15 minutes
                $this->retrieveData();
            }
            $this->data = json_decode(file_get_contents($this->file), true);
        }
        return $this->data;
    }

    private function getMedian($array)
    {
        sort($array);
        $result = $array[floor(count($array) / 2)];
        if(is_array($result) && isset($result['value'])){
            $result = $result['value'];
        }
        return $result;
    }

    private function retrieveData()
    {
        $emptyArray = [
            'value'     => 0,
            'date'      => null,
            'cpt'       => 0,
            'sum'       => 0,
            'avg'       => 0,
            'median'    => 0,
            'worst'     => null,
            'worstDate' => null,
        ];
        $compteur = [
            'labelHTML'         => $this->getName(),
            'label'             => $this->getName(true),
            'slug'              => Helper::slugify($this->getName(true), '-'),
            'id'                => $this->id,
            'lat'               => '',
            'lng'               => '',
            'dataCurYear'       => [],
            //'dataCurYearDates'  => [],
            //'dataCurYearValues' => [],
            'dataTotal'         => [],
            'dataTotalDates'    => [],
            'dataTotalValues'   => [],
            'dataTotalWithCplt' => [],
            'dataByYear'        => [],
            'sumCurYear'        => 0,
            'avgCurYear'        => 0,
            'sumTotal'          => 0,
            'avgTotal'          => 0,
            'medianTotal'       => 0,
            'recordYear'        => 0,
            'recordTotal'       => 0,
            'recordYearDate'    => '',
            'recordTotalDate'   => '',
            'worstYear'         => null,
            'worstTotal'        => null,
            'worstYearDate'     => '',
            'worstTotalDate'    => '',
            'lastValue'         => 0,
            'lastDate'          => '',
            'firstDate'         => null,
            'monthes'           => [],
            'days'              => [],
            'days-by-year'      => [],
            'weeks'             => [],
            'medianByYear'      => [],
            'sumByYear'         => [],
        ];
        $daysTotal = 0;
        $daysCurYear = 0;
        $data = @file_get_contents($this->archiveLink);
        $data = trim($data);
        $data = str_replace('{"int', ',{"int', $data);
        $data = substr($data, 1);
// --- correction 17 mai
        $dataToday = @file_get_contents($this->link);
        $dataToday = trim($dataToday);
        $dataToday = str_replace('{"int', ',{"int', $dataToday);
        $dataToday = substr($dataToday, 1);
        $data .= ','.$dataToday;
// ! --- correction 17 mai
        if(file_exists(dirname(__DIR__).'/dataarchive/MMM_EcoCompt_'.$this->id.'_Archive2020.json')){
            $dataArchive2020 = @file_get_contents(dirname(__DIR__).'/dataarchive/MMM_EcoCompt_'.$this->id.'_Archive2020.json');
            $dataArchive2020 = trim($dataArchive2020);
            $dataArchive2020 = str_replace('{"int', ',{"int', $dataArchive2020);
            $dataArchive2020 = substr($dataArchive2020, 1);
            $data = $dataArchive2020.','.$data;
        }
        $data = '['.$data.']';
        $doubles = []; // éviter les valeurs en double avec l'ajout des données 2020 en archive
        if($data && strlen($data) > 0){
            $values = json_decode($data);
            if(count($values) > 0){
                $values = $this->completeNoDataDays($values);
                uasort($values, [$this, 'orderByDO']);
                foreach($values as $val){
                    if(in_array($val->id, $doubles)){
                        continue; // éviter les valeurs en double avec l'ajout des données 2020 en archive
                    }
                    $doubles[] = $val->id;

                    $date = new CptDate($val->dateObserved);
                    $year = $date->format('Y');
                    $month = $date->format('m');
                    $week = $date->format('W');
                    $fDate = $date->format('d-m-Y');
                    $tsDate = $date->format('U');
                    $openDataValue = (int) $val->intensity;
                    if(isset($this->fix[$tsDate])){ // 14/10/2022 tableau de fix des valeurs
                        $cpt = (int) $this->fix[$tsDate];
                        $cptWithAsterisk = $cpt.'<sup>*</sup>';
                        $fixedValue = true;
                    }else{
                        $cpt = (int)$val->intensity;
                        $cptWithAsterisk = $cpt;
                    }
                    if($fixedValue && $cpt < 15){
                        continue;
                    }
                    if(!$fixedValue && intval($val->intensity) < 15){
                        continue; // garde fou contre les erreurs de relevé
                    }
                    $dayOfTheWeek = $date->format('N');
                    if(is_null($compteur['firstDate'])){
                        $compteur['firstDate'] = $fDate;
                    }
                    // --- gestion de l'année courante
                    if($year == _YEAR_){
                        if($cpt >= $compteur['recordYear']){
                            $compteur['recordYear'] = $cpt;
                            $compteur['recordYearDate'] = $fDate;
                        }
                        if(is_null($compteur['worstYear']) || $cpt < $compteur['worstYear']){
                            $compteur['worstYear'] = $cpt;
                            $compteur['worstYearDate'] = $fDate;
                        }
                        $compteur['sumCurYear'] += $cpt;
                        $daysCurYear++;
                        $compteur['dataCurYear'][$tsDate] = [
                            'date'      => $fDate,
                            'day'       => $dayOfTheWeek,
                            'isFerie'   => Helper::isFerie($date),
                            'value'     => $cpt,
                        ];
                    }
                    // --- gestion par mois
                    if(!isset($compteur["monthesY"][$year])){
                        $compteur["monthesY"][$year] = [];
                    }
                    if(!isset($compteur["monthesY"][$year][$month])){
                        $compteur['monthesY'][$year][$month] = $emptyArray;
                    }
                    if($cpt > $compteur['monthesY'][$year][$month]['value']){
                        $compteur['monthesY'][$year][$month]['value'] = $cpt;
                        $compteur['monthesY'][$year][$month]['date'] = $fDate;
                    }
                    if(is_null($compteur['monthesY'][$year][$month]['worst']) || $cpt < $compteur['monthesY'][$year][$month]['worst']){
                        $compteur['monthesY'][$year][$month]['worst'] = $cpt;
                        $compteur['monthesY'][$year][$month]['worstDate'] = $fDate;
                    }
                    $compteur['monthesY'][$year][$month]['cpt']++;
                    $compteur['monthesY'][$year][$month]['sum'] += $cpt;
                    // --- gestion des semaines
                    if(!isset($compteur["weeksY"][$year])){
                        $compteur["weeksY"][$year] = [];
                    }
                    if(!isset($compteur['weeksY'][$year][$week])){
                        $compteur['weeksY'][$year][$week] = $emptyArray;
                    }
                    if($cpt > $compteur['weeksY'][$year][$week]['value']){
                        $compteur['weeksY'][$year][$week]['value'] = $cpt;
                        $compteur['weeksY'][$year][$week]['date'] = $fDate;
                    }
                    if(is_null($compteur['weeksY'][$year][$week]['worst']) || $cpt < $compteur['weeksY'][$year][$week]['worst']){
                        $compteur['weeksY'][$year][$week]['worst'] = $cpt;
                        $compteur['weeksY'][$year][$week]['worstDate'] = $fDate;
                    }
                    $compteur['weeksY'][$year][$week]['cpt']++;
                    $compteur['weeksY'][$year][$week]['sum'] += $cpt;
                    // --- gestion du jour de la semaine par années
                    if(!isset($compteur['days-by-year'][$year])){
                        $compteur['days-by-year'][$year] = [];
                    }
                    if(!isset($compteur['days-by-year'][$year][$dayOfTheWeek])){
                        $compteur['days-by-year'][$year][$dayOfTheWeek] = $emptyArray;
                    }
                    if($cpt > $compteur['days-by-year'][$year][$dayOfTheWeek]['value']){
                        $compteur['days-by-year'][$year][$dayOfTheWeek]['value'] = $cpt;
                        $compteur['days-by-year'][$year][$dayOfTheWeek]['date'] = $fDate;
                    }
                    if(is_null($compteur['days-by-year'][$year][$dayOfTheWeek]['worst']) || $cpt < $compteur['days-by-year'][$year][$dayOfTheWeek]['worst']){
                        $compteur['days-by-year'][$year][$dayOfTheWeek]['worst'] = $cpt;
                        $compteur['days-by-year'][$year][$dayOfTheWeek]['worstDate'] = $fDate;
                    }
                    $compteur['days-by-year'][$year][$dayOfTheWeek]['cpt']++;
                    $compteur['days-by-year'][$year][$dayOfTheWeek]['sum'] += $cpt;
                    // --- gestion du jour de la semaine
                    if(!isset($compteur['days'][$dayOfTheWeek])){
                        $compteur['days'][$dayOfTheWeek] = $emptyArray;
                    }
                    if($cpt > $compteur['days'][$dayOfTheWeek]['value']){
                        $compteur['days'][$dayOfTheWeek]['value'] = $cpt;
                        $compteur['days'][$dayOfTheWeek]['date'] = $fDate;
                    }
                    if(is_null($compteur['days'][$dayOfTheWeek]['worst']) || $cpt < $compteur['days'][$dayOfTheWeek]['worst']){
                        $compteur['days'][$dayOfTheWeek]['worst'] = $cpt;
                        $compteur['days'][$dayOfTheWeek]['worstDate'] = $fDate;
                    }
                    $compteur['days'][$dayOfTheWeek]['cpt']++;
                    $compteur['days'][$dayOfTheWeek]['sum'] += $cpt;
                    // --- gestion depuis le début
                    if($cpt >= $compteur['recordTotal']){
                        $compteur['recordTotal'] = $cpt;
                        $compteur['recordTotalDate'] = $fDate;
                    }
                    if(is_null($compteur['worstTotal']) || $cpt < $compteur['worstTotal']){
                        $compteur['worstTotal'] = $cpt;
                        $compteur['worstTotalDate'] = $fDate;
                    }
                    $compteur['sumTotal'] += $cpt;
                    $daysTotal++;
                    $compteur['dataTotal'][$fDate] = $cpt;
                    $compteur['dataTotalDates'][$date->format('Ymd')] = $fDate;
                    $compteur['dataTotalValues'][$date->format('Ymd')] = $cpt;
                    $compteur['dataTotalWithCplt'][$fDate] = [
                        'date'          => $fDate,
                        'day'           => $dayOfTheWeek,
                        'isFerie'       => Helper::isFerie($date),
                        'value'         => $cptWithAsterisk,
                        'originalvalue' => $openDataValue,
                    ];

                }
                $compteur['lastDate'] = $fDate;
                $compteur['lastValue'] = $cpt;
                if(isset($val->location) && is_object($val->location) && isset($val->location->coordinates) && is_array($val->location->coordinates)){
                    $compteur['lat'] = $val->location->coordinates[1];
                    $compteur['lng'] = $val->location->coordinates[0];
                }
                $compteur['avgCurYear']  = ($daysCurYear > 0 ? intval($compteur['sumCurYear'] / $daysCurYear) : 0);
                $compteur['avgTotal']  = ($daysTotal > 0 ? intval($compteur['sumTotal'] / $daysTotal) : 0);
                $compteur['medianTotal'] = $this->getMedian($compteur['dataTotalValues']);
            }
        }
        ksort($compteur['dataTotalDates']);
        ksort($compteur['dataTotalValues']);
        $compteur['maxAvgMonthY'] = 0;
        $monthesRecord = [];
        if(count($compteur['monthesY']) > 0){
            foreach($compteur['monthesY'] as $year=> $monthes){
                if(count($monthes) > 0){
                    for($month = 1; $month <= 12; $month++){
                        if($month < 10){
                            $month = '0'.$month;
                        }
                        if(!isset($monthesRecord[$month])){
                            $monthesRecord[$month] = [
                                "value" => 0,
                                'date'  => null,
                            ];
                        }
                        if(!isset($monthes[$month])){
                            $compteur['monthesY'][$year][$month] = $emptyArray;
                        }else{
                            $val = $monthes[$month];
                            if($val['cpt'] > 0){
                                $compteur['monthesY'][$year][$month]['avg'] = intval($val['sum'] / $val['cpt']);
                                if($compteur['monthesY'][$year][$month]['avg'] > $compteur['maxAvgMonthY']){
                                    $compteur['maxAvgMonthY'] = $compteur['monthesY'][$year][$month]['avg'];
                                }
                            }
                            if($val['value'] >= $monthesRecord[$month]["value"]){
                                $monthesRecord[$month] = [
                                    "value" => $val['value'],
                                    'date'  => $val['date'],
                                ];
                            }
                        }
                    }
                    ksort($compteur['monthesY'][$year]);
                }
            }
        }
        $compteur['monthesRecord'] = $monthesRecord;
        if(isset($compteur['monthesY'][_YEAR_])){
            $compteur['monthes'] = $compteur['monthesY'][_YEAR_];
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
                }
            }
        }
        if(count($compteur['dataTotal'])){
            foreach($compteur['dataTotal'] as $date => $value){
                $year = substr($date, -4);
                if(!isset($compteur['dataByYear'][$year])){
                    $compteur['dataByYear'][$year] = [];
                }
                $compteur['dataByYear'][$year][] = $value;
            }
            foreach($compteur['dataByYear'] as $year => $values){
                ksort($compteur['dataByYear'][$year]);
                $compteur['medianByYear'][$year] = $this->getMedian($values);
                $compteur['sumByYear'][$year] = array_sum($values);
            }
        }
        $compteur['maxAvgWeeksY'] = 0;
        if(count($compteur['weeksY']) > 0){
            foreach($compteur['weeksY'] as $year => $weeks){
                for($week = 1; $week <= 53; $week++){
                    if($week < 10){
                        $week = '0'.$week;
                    }
                    if(($week == 53 || $week == 52) && $year == date('Y')){
                        $compteur['weeksY'][$year][$week] = $emptyArray;
                    }
                    if(!isset($compteur['weeksY'][$year][$week])){
                        $compteur['weeksY'][$year][$week] = $emptyArray;
                    }
                    $val = $compteur['weeksY'][$year][$week];
                    if($val['cpt'] > 0){
                        $compteur['weeksY'][$year][$week]['avg'] = intval($val['sum'] / $val['cpt']);
                        if($compteur['weeksY'][$year][$week]['avg'] > $compteur['maxAvgWeeksY']){
                            $compteur['maxAvgWeeksY'] = $compteur['weeksY'][$year][$week]['avg'];
                        }
                    }
                }
                ksort($compteur['weeksY'][$year]);
            }
        }
        if(isset($compteur['weeksY'][_YEAR_])){
            $compteur['weeks'] = $compteur['weeksY'][_YEAR_];
        }
        $file = fopen($this->file, 'w+');
        fwrite($file, json_encode($compteur, true));
        fclose($file);
    }

    private function getChartDates(int $days = 0, int $nb = 0) : array
    {
        $retour = [];
        $dateLimit = new \DateTime();
        $dateLimit->modify('-'.$days.' days');
        $dateLimit = $dateLimit->format('Ymd');
        if($nb > 0) {
            $dateEnd = new \DateTime();
            $dateEnd->modify('-'.$days.' days');
            $dateEnd->modify('+'.$nb.' days');
            $dateEnd = $dateEnd->format('Ymd');
        }else{
            $dateEnd = date('Ymd');
        }
        $dates = $this->get('dataTotalDates');
        if(is_array($dates) && count($dates) > 0){
            foreach($dates as $date){
                $exp = explode('-', $date);
                if(count($exp) == 3){
                    if($exp[2].$exp[1].$exp[0] > $dateLimit && $exp[2].$exp[1].$exp[0] <= $dateEnd) {
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
            foreach($data as $ts => $value){
                $sum += $value["value"];
                $retour[$value["date"]] = $sum;
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

    public function getDayByType()
    {
        $retourJo = [];
        $retourJc = [];
        $data = $this->get('dataCurYear');
        if(count($data) > 0){
            foreach($data as $k => $v){
                if($v['day'] < 6 && !$v['isFerie']){
                    $retourJo[] = $v;
                }else{
                    $retourJc[] = $v;
                }
            }
        }
        return [$retourJo, $retourJc];
    }

    public function get($item, $days = 14, $nbDays = 0)
    {
        if(is_null($this->data)){
            $this->getData();
        }
        $retour = null;
        if($item == 'chartDates'){
            $retour = $this->getChartDates($days, $nbDays);
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

    public function completeNoDataDays($values)
    {
        $verif = [];
        foreach($values as $val) {
            $date = new CptDate($val->dateObserved);
            $verif[] = (int)$date->format('U');
        }
        foreach($this->fix as $d => $v) {
            if(!in_array((int)$d, $verif)){
                $tmp = new \stdClass();
                $tmp->id = "Manual_".$this->id.'_'.$d;
                $tmp->intensity = (int)$v;
                $date = \DateTime::createFromFormat('U', $d);
                $date->modify('+1 day');
                $date2 = clone $date;
                $date2->modify('+1 day');
                $date = $date->format('Y-m-d\T00:00:00').'/'.$date2->format('Y-m-d\T00:00:00');
                $tmp->dateObserved = $date;
                $values[] = $tmp;
            }
        }
        return $values;
    }

    public function getAvailableYears()
    {
        return array_keys($this->get('weeksY'));
    }
}
