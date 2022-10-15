<?php
namespace App;

class Compteurs
{
    private static $_instance = null;
    private $compteurs = [
        'XTH19101158'   => [
            'label'     => 'Albert 1<sup>er</sup>',
            'address'   => "Place Albert 1<sup>er</sup>, Montpellier",
            'color'     => '#8540f5',
            'totem'     => true,
            'commune'   => 'Montpellier',
        ],
        'X2H21070348'   => [
            'label'     => 'Albert Einstein',
            'address'   => '',
            'color'     => '#998888',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H22043033'   => [
            'label'     => 'Allée de la Méditerranée',
            'address'   => '',
            'color'     => '#998888',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H21070350'   => [
            'label'     => 'Arnel',
            'address'   => '',
            'color'     => '#aaddbb',
            'totem'     => false,
            'commune'   => 'Villeneuve-les-Maguelone',
        ],
        'X2H19070220'   => [
            'label'     => 'Beracasa',
            'address'   => 'Allée Alegria Beracasa, Montpellier',
            'color'     => '#3d8bfd',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        /*
        'XTH21070328'   => [
            'label'     => 'Clapiers (Totem Simone Veil)',
            'address'   => '',
            'color'     => '#00ffff',
            'totem'     => true,
            'commune'   => 'Clapiers',
        ],
        /* */
        'X2H22043031'   => [
            'label'     => 'Castelnau - Aube-Rouge 1',
            'address'   => '',
            'color'     => '#de5c9d',
            'totem'     => false,
            'commune'   => 'Castelnau-le-Lez',
        ],
        'X2H22043032'   => [
            'label'     => 'Castelnau - Aube-Rouge 2',
            'address'   => '',
            'color'     => '#de5c9d',
            'totem'     => false,
            'commune'   => 'Castelnau-le-Lez',
        ],
        'X2H20042633'   => [
            'label'     => 'Celleneuve',
            'address'   => '137 avenue de Lodève, Montpellier',
            'color'     => '#de5c9d',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H20063163'   => [
            'label'     => 'Delmas 1',
            'address'   => '73 Avenue François Delmas, Montpellier',
            'color'     => '#fd9843',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H20063164'   => [
            'label'     => 'Delmas 2',
            'address'   => '73 Avenue François Delmas, Montpellier',
            'color'     => '#ffcd39',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H21070344'   => [
            'label'     => 'Figuerolles',
            'address'   => '',
            'color'     => '#aa77dd',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H20063162'   => [
            'label'     => 'Gerhardt',
            'address'   => '1 rue Gerhardt, Montpellier',
            'color'     => '#479f76',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H21070346'   => [
            'label'     => 'Jacou',
            'address'   => '',
            'color'     => '#123456',
            'totem'     => false,
            'commune'   => 'Jacou',
        ],
        'X2H22043030'   => [
            'label'     => 'Juvignac - Europe',
            'address'   => '',
            'color'     => '#123456',
            'totem'     => false,
            'commune'   => 'Juvignac',
        ],
        'X2H21070349'   => [
            'label'     => 'Lapeyronie',
            'address'   => '',
            'color'     => '#aaddbb',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H20042635'   => [
            'label'     => 'Lattes 1',
            'address'   => 'Avenue Georges Frêche, Pérols',
            'color'     => '#4dd4ac',
            'totem'     => false,
            'commune'   => 'Pérols',
        ],
        'X2H20042634'   => [
            'label'     => 'Lattes 2',
            'address'   => 'Avenue Georges Frêche, Pérols',
            'color'     => '#3dd5f3',
            'totem'     => false,
            'commune'   => 'Pérols',
        ],
        'X2H20042632'   => [
            'label'     => 'Lavérune',
            'address'   => 'D5E1, Lavérune',
            'color'     => '#6c757d',
            'totem'     => false,
            'commune'   => 'Lavérune',
        ],
        'XTH21015106'   => [
            'label'     => 'Lèques (Totem Lavérune)',
            'address'   => '',
            'color'     => '#99aaff',
            'totem'     => true,
            'commune'   => 'Montpellier',
        ],
        'X2H21070351'   => [
            'label'     => 'Mas de Gau',
            'address'   => '',
            'color'     => '#bbaaaa',
            'totem'     => false,
            'commune'   => 'Lattes',
        ],
        'X2H20104132'   => [
            'label'     => 'Méric',
            'address'   => 'Rue de la Draye, Montpellier',
            'color'     => '#336600',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H21070343'   => [
            'label'     => 'Montferrier 1',
            'address'   => '',
            'color'     => '#996633',
            'totem'     => false,
            'commune'   => 'Montferrier-sur-Lez',
        ],
        'X2H21070347'   => [
            'label'     => 'Montferrier 2',
            'address'   => '',
            'color'     => '#000000',
            'totem'     => false,
            'commune'   => 'Montferrier-sur-Lez',
        ],
        'X2H21070345'   => [
            'label'     => 'Pavelet',
            'address'   => '',
            'color'     => '#dd77bb',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H21070341'   => [
            'label'     => 'Père Soulas 1',
            'address'   => '',
            'color'     => '#0000dd',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H21070342'   => [
            'label'     => 'Père Soulas 2',
            'address'   => '',
            'color'     => '#ee0000',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H22043034'   => [
            'label'     => 'Renouvier (bande cyclable)',
            'address'   => '',
            'color'     => '#ee0000',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H22043035'   => [
            'label'     => 'Renouvier (chaussée)',
            'address'   => '',
            'color'     => '#abef99',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H21111120'   => [
            'label'     => 'Saint-Jaumes nord',
            'address'   => '',
            'color'     => '#abef99',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H21111121'   => [
            'label'     => 'Saint-Jaumes sud',
            'address'   => '',
            'color'     => '#15202b',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H22043029'   => [
            'label'     => 'Thomassy (RM65)',
            'address'   => '',
            'color'     => '#112222',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        'X2H20063161'   => [
            'label'     => 'Vieille-Poste',
            'address'   => '1211 rue de la Vieille-Poste, Montpellier',
            'color'     => '#031633',
            'totem'     => false,
            'commune'   => 'Montpellier',
        ],
        /* */
    ];

    private function __construct(){}

    public function getCompteurs()
    {
        $fix = (Fix::getInstance())->getData();
        $retour = [];
        if(count($this->compteurs) > 0){
            foreach($this->compteurs as $id => $cpt){
                $retour[$id] = new Compteur($id, $cpt['label'], $cpt['address'], $cpt['color'], $cpt['totem'], $cpt['commune'], $fix);
            }
        }
        return $retour;
    }

    public function getCompteurBySlug($slug = null)
    {
        $retour = null;
        if(count($this->compteurs) > 0){
            foreach($this->compteurs as $id => $cpt){
                if(Helper::slugify(strip_tags($cpt['label']), '-') == $slug){
                    $retour = new Compteur($id, $cpt['label'], $cpt['address'], $cpt['color'], $cpt['totem'], $cpt['commune']);
                }
            }
        }
        return $retour;
    }

    public function getCompteurId($id = '')
    {
        $retour = null;
        if(count($this->compteurs) > 0){
            foreach($this->compteurs as $k => $cpt){
                if($id == $k){
                    $retour = new Compteur($id, $cpt['label'], $cpt['address'], $cpt['color'], $cpt['commune']);
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

    public function getAllByDates(int $year = null)
    {
        $retour = [
            'dates' => [],
            'data'  => [],
        ];
        $dataTmp = [];
        $compteurs = $this->getCompteurs();
        if(is_null($year) || ($year < 2020 && $year > date('Y'))){
            $year = date('Y');
        }
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
            foreach($this->compteurs as $k => $cpt){
                $retour[$k] = [
                    'id'    => $k,
                    'name'  => strip_tags($cpt['label']),
                    'color' => $cpt['color'],
                ];
            }
        }
        return $retour;
    }

    public function getWeekWeekend($year = null, $item = 'avg')
    {
        $retour = [
            'week'      => 0,
            'weekend'   => 0,
        ];
        $count = count($this->compteurs);
        if($count){
            $compteurs = $this->getCompteurs();
            foreach($compteurs as $k => $cpt){
                $tmp = $cpt->getWeekWeekend($year, $item, true);
                $retour['week'] += $tmp['week'];
                $retour['weekend'] += $tmp['weekend'];
            }
            $retour['week'] = intval($retour['week'] / $count);
            $retour['weekend'] = intval($retour['weekend'] / $count);
        }
        return $retour;
    }
}
