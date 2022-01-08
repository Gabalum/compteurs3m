<?php
namespace App;
require_once(dirname(__DIR__, 2).'/vendor/autoload.php');
ob_start();
$handler = fopen("php://output", 'w');
$data = (new Meteo(2500))->getData();
$weather = (new Meteo())->getWeather();
if(is_array($data) && count($data) > 0){
    fputcsv($handler,['date', 'temp. 06h00', 'temp. 12h00', 'temp. 18h00',
        'temp. 06h00 (brut)', 'temp. 12h00 (brut)', 'temp. 18h00 (brut)',
        'rr3 (06h)', 'rr6 (06h)', 'rr12 (06h)', 'rr24 (06h)', 'ff (06h)', 'dd (06h)', 'cod_tend (06h)',
        'rr3 (12h)', 'rr6 (12h)', 'rr12 (12h)', 'rr24 (12h)', 'ff (12h)', 'dd (12h)', 'cod_tend (12h)',
        'rr3 (18h)', 'rr6 (18h)', 'rr12 (18h)', 'rr24 (18h)', 'ff (18h)', 'dd (18h)', 'cod_tend (18h)',
        'weather (06h)', 'weather (12h)', 'weather (18h)',
    ]);
    foreach($data as $k => $elt){
        $elt['weather_06'] = (isset($weather[$k]) ? $weather[$k]['06']['code'] : '-');
        $elt['weather_12'] = (isset($weather[$k]) ? $weather[$k]['12']['code'] : '-');
        $elt['weather_18'] = (isset($weather[$k]) ? $weather[$k]['18']['code'] : '-');
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
