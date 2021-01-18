<?php
namespace App;

class Compteurs
{
    private static $_instance = null;
    private $compteurs = [];
    private $addresses = [
        'XTH19101158'   => "Place Albert 1<sup>er</sup>, Montpellier",
        'X2H19070220'   => "Allée Alegria Berracasa, Montpellier",
        'X2H20042633'   => "137 avenue de Lodève, Montpellier",
        'X2H20063163'   => "73 Avenue François Delmas, Montpellier",
        'X2H20063164'   => "73 Avenue François Delmas, Montpellier",
        'X2H20063162'   => "1 rue Gerhardt, Montpellier",
        'X2H20042635'   => "Avenue Georges Frêche, Pérols",
        'X2H20042634'   => "Avenue Georges Frêche, Pérols",
        'X2H20042632'   => "D5E1, Lavérune",
        'X2H20063161'   => "1211 rue de la Vieille-Poste, Montpellier",
    ];

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
                $retour[$id]->setAddress($this->addresses[$id]);
            }
        }
        return $retour;
    }

    public function getCompteurBySlug($slug = null)
    {
        $retour = null;
        if(count($this->compteurs) > 0){
            foreach($this->compteurs as $id => $label){
                if(Helper::slugify(strip_tags($label), '-') == $slug){
                    $retour = new Compteur($id, $label);
                    $retour->setAddress($this->addresses[$id]);
                    break;
                }
            }
        }
        return $retour;
    }

    public function getCompteurId($id = '')
    {
        $retour = null;
        if(count($this->compteurs) > 0){
            foreach($this->compteurs as $k => $label){
                if($id == $k){
                    $retour = new Compteur($id, $label);
                    $retour->setAddress($this->addresses[$id]);
                    break;
                }
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

    public function getAllByDates()
    {
        $retour = [
            'dates' => [],
            'data'  => [],
        ];
        $dataTmp = [];
        $compteurs = $this->getCompteurs();
        $year = date('Y');
        if(count($compteurs) > 0){
            foreach($compteurs as $k => $compteur){
                $data = $compteur->get('dataTotal');
                if(count($data) > 0){
                    if(!isset($retour['data'][$k])){
                        $dataTmp[$k] = [];
                        $retour['data'][$k] = [];
                    }
                    foreach($data as $date => $value){
                        $date = new \DateTime($date);
                        if($date->format('Y') == $year){
                            $retour['dates'][$date->format('U')] = $date->format('d-m-Y');
                            $dataTmp[$k][$date->format('U')] = $value;
                        }
                    }
                }
            }
        }
        ksort($retour['dates']);
        if(count($dataTmp)){
            foreach($dataTmp as $k => $v){
                foreach($retour['dates'] as $u => $d){
                    if(!isset($dataTmp[$k][$u])){
                        $dataTmp[$k][$u] = null;
                    }
                }
                ksort($dataTmp[$k]);
            }
            foreach($dataTmp as $k => $v){
                $retour['data'][$k] = array_values($v);
            }
        }
        return $retour;
    }

    public function getLabels()
    {
        $retour = [];
        if(count($this->compteurs) > 0){
            foreach($this->compteurs as $k => $v){
                $retour[$k] = [
                    'name' => strip_tags($v),
                    'color' => Helper::colorGenerator(),
                ];
            }
        }
        return $retour;
    }
}
