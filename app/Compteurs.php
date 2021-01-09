<?php
namespace App;

class Compteurs
{
    private static $_instance = null;
    private $compteurs = [];

    private function __construct()
    {
        $this->compteurs = [
            'XTH19101158'   => "Albert 1<sup>er</sup>",
            /* */
            'X2H19070220'   => "Berracasa",
            'X2H20042633'   => "Celleneuve",
            'X2H20063163'   => "Delmas 1",
            'X2H20063164'   => "Delmas 2",
            'X2H20063162'   => "Gerhardt",
            'X2H20042635'   => "Lattes 1",
            'X2H20042634'   => "Lattes 2",
            'X2H20042632'   => "Lavérune",
            'X2H20063161'   => "Vieille-Poste",
            /* */
        ];
    }

    public function getCompteurs()
    {
        $retour = [];
        if(count($this->compteurs) > 0){
            foreach($this->compteurs as $id => $label){
                $retour[$id] = new Compteur($id, $label);
            }
        }
        return $retour;
    }

    public static function getInstance()
    {
        if(is_null(self::$_instance)) {
            self::$_instance = new Compteurs();
        }
        return self::$_instance;
    }
}
