<?php
namespace App;

require_once('vendor/autoload.php');

use Dotenv\Dotenv;

if(!defined('_YEAR_')){
    define('_YEAR_', intval(date('Y')));
}
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$expEnv = explode('/', __DIR__);
if(isset($expEnv[1]) && $expEnv[1] == 'Users'){
    define('_ENV_', 'local');
    define('_BASE_URL_', 'http://compteurs.lan/');
}else{
    define('_ENV_', 'prod');
    define('_BASE_URL_', 'https://compteurs.velocite-montpellier.fr/');
}
