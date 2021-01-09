<?php
namespace App;
require_once('../vendor/autoload.php');
ob_start();
$handler = fopen("php://output", 'w');
$data = (new Meteo())->getData();
if(is_array($data) && count($data) > 0){
    fputcsv($handler,['date', '06h00', '12h00', '18h00']);
    foreach($data as $elt){
        fputcsv($handler, $elt);
    }
}
$streamSize = ob_get_length();
fclose($handler);
/* */
header('Content-Description: File Transfer');
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=temperatures-totems-".date('Y-m-d').".csv");
header("Pragma: public");
header("Expires: 0");
header('Content-Length: ' . $streamSize);
header('Cache-Control: must-revalidate');
/* */
ob_end_flush();
