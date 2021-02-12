<?php
require_once('../vendor/autoload.php');
use App\Compteurs;
ob_start();
$compteurs = (Compteurs::getInstance())->getCompteurs();
$dates = [];
$totems = [];
$values = [];
$last = null;
if(count($compteurs) > 0){
    foreach($compteurs as $k => $compteur){
        $totems[$k] = $compteur->get('label');
        $data = $compteur->get('dataTotal');
        foreach($data as $date => $val){
            $x = explode('-', $date);
            $dateKey = $x[2].$x[1].$x[0];
            $values[$dateKey]['date'] = $date;
            if(!isset($values[$dateKey])){
                $values[$dateKey] = [];
            }
            $values[$dateKey][$k] = $val;
        }
    }
    $last = $k;
}
ksort($values);
$handler = fopen("php://output", 'w');
$data = [];
if(count($values) > 0){
    fputcsv($handler, array_merge(['date'], $totems));
    foreach($values as $val){
        $item = [];
        $item[] = $val['date'];
        foreach($totems as $k => $v){
            if(isset($val[$k])){
                $item[] = $val[$k];
            }else{
                $item[] = '-';
            }
        }
        fputcsv($handler, $item);
    }
}
$streamSize = ob_get_length();
fclose($handler);
/* */
header('Content-Description: File Transfer');
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=data-totems-".date('Y-m-d').".csv");
header("Pragma: public");
header("Expires: 0");
header('Content-Length: ' . $streamSize);
header('Cache-Control: must-revalidate');
/* */
ob_end_flush();
