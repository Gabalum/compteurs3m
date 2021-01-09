<?php
require_once('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$apiKey = $_ENV['HISTORYWEATHERKEY'];
$cityId = 2992166;
$cityCountry = 'FR';
$lat = 43.616830;
$lon = 3.873626;
$date = (new DateTime())->modify('-14 days');
$dt = $date->format('U');
$url = 'https://api.openweathermap.org/data/2.5/onecall/timemachine?lat='.$lat.'&lon='.$lon.'&dt='.$dt.'&appid='.$apiKey;
var_dump($url);die();
$data = file_get_contents('http://history.openweathermap.org/data/2.5/history/city?q=Montpellier,FR&appid='.$apiKey);
var_dump($data);die();
