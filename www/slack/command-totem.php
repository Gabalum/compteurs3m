<?php
namespace App;

use \DateTime;

require_once('../../bootstrap.php');

$slug = null;
if(isset($_POST) && count($_POST) > 0 && isset($_POST['text']) && strlen($_POST['text']) > 0){
    $slug = trim(strip_tags($_POST['text']));
}elseif(count($_GET) > 0 && isset($_GET['text']) && strlen($_GET['text']) > 0) {
    $slug = trim(strip_tags($_GET['text']));
}
$totem = null;
if($slug == 'simone-veil' || $slug == 'sisi'){
    $totem = TotemSisi::getInstance();
}elseif($slug == 'laverune' || $slug == 'lala'){
    $totem = TotemLala::getInstance();
}elseif($slug == 'toto' ||$slug == 'albert' || $slug == 'albert-1er'){
    $totem = TotemAlbert::getInstance();
}
$message = "Désolé je n'ai pas compris la demande, les options sont : toto, sisi ou lala";
if(!is_null($totem)){
    $data = $totem->getData();
    if(is_array($data) && count($data)){
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        $last = end($data);
        $last = end($last);
        $last = end($last);
        $dateDernierReleve = DateTime::createFromFormat('d/m/Y', $last['dateOrig']);
        $ddRCopy = DateTime::createFromFormat('d/m/Y', $last['dateOrig']);
        $ddRCopy->setTime(0, 0, 0);
        $diff = $today->diff($ddRCopy);
        $jrs = abs(intval($diff->format("%R%a")));
        $time = explode(':', $last['heure']);
        $message = '*Dernier relevé pour le totem '.$totem->getName(true).' ';
        if($jrs == 0){
            $message .= "aujourd'hui";
            $leJour = "aujourd'hui";
            $totalMsg = 'Total hier';
            $daySince = $totem->daySince();
        }elseif($jrs == 1){
            $message .= "hier";
            $leJour = "hier";
            $totalMsg = 'Total la veille';
            $daySince = $totem->daySince(time() - 86400);
        }else{
            $message .= "le ".dateReplace($dateDernierReleve->format('d F Y'));
            $leJour = "ce jour là";
            $totalMsg = 'Total la veille';
            $daySince = $totem->daySince($dateDernierReleve->format('U'));
        }
        $message .= " à ".$time[0].'h'.$time[1].'* : '."\n";
        $total = $last['total'];
        $moy = round($total/$daySince);
        if(date('Y') == $totem->getLaunchYear()){
            $since = ' depuis le '.$totem->getFirstDay().' (installation du Totem)';
        }else{
            $since = ' depuis le début de l\'année';
        }
        $message .= $last['instant'].' :jsudd: et :jsudd_bleu: '.$leJour.' et '.$total.$since.' :star-struck:';
        $message .= "\nSoit une moyenne de ".$moy." passage".($moy > 1?"s":'')." par jour";
    }else{
        $message = "Désolé je n'ai pas de donnée à vous transmettre";
    }
}
header('Content-type: application/json');
echo json_encode([
	'response_type' => 'in_channel',
	'text'			=> $message,
]);
