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
            $dates[] = $date;
            $values[$date][$k] = $val;
        }
    }
    $last = $k;
}
$dates = array_unique($dates);
$handler = fopen("php://output", 'w');
$data = [];
if(count($dates) > 0){
    fputcsv($handler, array_merge(['date'], $totems));
    //$data[] = 'date,'.implode(',', $totems);
    foreach($dates as $date){
        $item = [];
        if(isset($values[$date])){
            $item[] = $date;
            foreach($totems as $k => $v){
                if(isset($values[$date][$k])){
                    $item[] = $values[$date][$k];
                }else{
                    $item[] = '-';
                }
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
