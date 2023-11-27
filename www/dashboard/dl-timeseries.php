<?php
    namespace App;
    require_once(dirname(__DIR__, 2).'/bootstrap.php');
    $compteurs = (Compteurs::getInstance())->getList();
    $lines = [];
    $header = ['id du compteur', 'nom du compteur', 'date', 'heure', 'effectif'];
    foreach($compteurs as $id => $cpt){
        $ts = (new Timeserie($id))->getData();
        if(is_array($ts) && isset($ts['raw']) && is_array($ts['raw']) && count($ts['raw']) > 0){
            foreach($ts['raw'] as $date => $value){
                $date = new \DateTime($date);
                $lines[] = [$id, Helper::slugify($cpt['label']), $date->format('d-m-Y'), $date->format('H:m:s'), $value];
            }
        }
    }
    ob_start();
    $handler = fopen("php://output", 'w');
    fputcsv($handler, $header);
    if(count($lines) > 0){
        foreach($lines as $l){
            fputcsv($handler, $l);
        }
    }
    $streamSize = ob_get_length();
    fclose($handler);
    /* */
    header('Content-Description: File Transfer');
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=data-timeseries-".date('Y-m-d').".csv");
    header("Pragma: public");
    header("Expires: 0");
    header('Content-Length: ' . $streamSize);
    header('Cache-Control: must-revalidate');
    /* */
    ob_end_flush();
