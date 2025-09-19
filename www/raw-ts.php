<?php
require_once('../vendor/autoload.php');
use App\Compteurs;
use App\Timeserie;
ob_start();
$compteurs = (Compteurs::getInstance())->getCompteurs();
$ts = [];
if (count($compteurs) > 0) {
    foreach ($compteurs as $k => $compteur) {
        $serie = new Timeserie($compteur->getId());
        $ts[$k] = $serie->getData();
    }
}
echo json_encode($ts);