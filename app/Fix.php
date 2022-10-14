<?php
namespace App;

class Fix
{
    private static $_instance = null;
    private $data = null;
    private $file = '';

    private function __construct() {}

    public static function getInstance()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new Fix();
        }
        return self::$_instance;
    }

    public function getData()
    {
        $this->file = dirname(__DIR__).'/data/fix.csv';
        if($this->data == null){
            //$this->data = [];
            if(isset($_GET['reload']) || isset($_GET['reloadfix']) ||
            !file_exists($this->file) || (date('dmY', filemtime($this->file)) !== date('dmY') || time() - filemtime($this->file) > 900) ) {
                $elt = @file_get_contents($_ENV['SHEET_FIX']);
                $handle = fopen($this->file, 'w+');
                fwrite($handle, $elt);
                fclose($handle);
            }
            $handle = fopen($this->file, 'r');
            $first = true;
            $timezone = new \DateTimeZone('Europe/Paris');
            while($line = fgetcsv($handle)){
                if($first){
                    $first = false;
                    continue;
                }
                $date = (\DateTime::createFromFormat('d/m/Y', $line['2'], $timezone))->setTime(0,0,0)->format('U');
                if(!isset($this->data[$line[1]])){
                    $this->data[$line[1]] = [];
                }
                $this->data[$line[1]][$date] = $line[3];
            }
            fclose($handle);
        }
        return $this->data;
    }
}
