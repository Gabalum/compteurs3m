<?php
    namespace App;
    require_once('./bootstrap.php');
    $compteurs = (Compteurs::getInstance())->getIds();
    foreach($compteurs as $cpt){
        echo '['.date('d/m/Y H:i:s').'] '.'Debut traitement '.$cpt.PHP_EOL;
        (new Timeserie($cpt))->process();
        echo '['.date('d/m/Y H:i:s').'] '.'Fin traitement '.$cpt.PHP_EOL;
    }
