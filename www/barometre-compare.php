<?php
function sortCmp($a, $b)
{
    return ($a['valeur'] <= $b['valeur'] ? -1 : 1);
}
function getCountdown($couic = false) 
{
    $today = new DateTime();
    $lastDay = new DateTime('2025-06-02');
    if($today > $lastDay) {
        return null;
    }
    $diff = $today->diff($lastDay);
    if($couic) {
        return intval(100*(94-$diff->days) / 94);
    }
    return 'Il reste '.$diff->days.' jour'.($diff->days > 1 ? 's' : '').' avant la fin du baromètre (2 juin 2025).<br>';
}

$thresholdDays = getCountdown(true);
$countdown = getCountdown();
$m3m = [
    34172 => 3632, // Montpellier
    34022 => 61, // Baillargues
    34027 => 3, // Beaulieu
    34057 => 326, // Castelnau
    34058 => 86, // Castries
    34077 => 141, // Clapiers
    34090 => 80, // Le Crès
    34087 => 21, // Cournonsec
    34088 => 56, // Cournonterral
    34095 => 82, // Fabrègues
    34116 => 73, // Grabels
    34120 => 122, // Jacou
    34123 => 94, // Juvignac
    34129 => 125, // Lattes
    34134 => 58, // Lavérune
    34164 => 9, // Montaud
    34169 => 91, // Montferrier
    34179 => 59, // Murviel
    34198 => 70, // Pérols
    34202 => 80, // Pignan
    34217 => 95, // Prades
    34227 => 5, // Restinclières
    34244 => 15, // Saint-Brès
    34249 => 61, // Saint-Drézéry
    34256 => 44, // Saint-Geniès
    34259 => 94, // Saint-Georges
    34270 => 140, // Saint-Jean-de-Védas
    34295 => 61, // Saussan
    34307 => 44, // Sussargues
    34327 => 88, // Vendargues
    34337 => 70, // Villeneuve
];
$pdlor = [
    34154 => 190, // Mauguio
    34050 => 3, // Candillargues
    34344 => 140, // La Grande-Motte
    34127 => 2, // Lansargues
    34176 => 52, // Mudaison
    34192 => 66, // Palavas
    34240 => 76, // Saint-Aunes
    34321 => 1, // Valergues
];
$ccgpsl = [
    34014 => 10, // Assas
    34078 => 7, // Claret
    34082 => 58, // Combaillaux
    34102 => 0, // Fontanès
    34118 => 3, // Guzargues
    34131 => 1, // Lauret
    34152 => 9, // Mas-de-Londres
    34153 => 53, // Les Matelles
    34177 => 1, // Murles
    34185 => 10, // ND Londres
    34242 => 1, // Saint-Bauzille
    34247 => 56, // Saint-Clément
    34255 => 76, // Saint-Gély-du-Fesc
    34266 => 2, // Saint-Jean-de-Cuculles
    34274 => 58, // Saint-Martin
    34276 => 69, // Saint-Mathieu
    34290 => 0, // Saint-Vincent
    34248 => 2, // Sainte-Croix
    34309 => 114, // Teyran
    34314 => 2, // Le Triadou
    34320 => 25, // Vailhauquès
    34322 => 4, // Valflaunès
    34342 => 17, // Viols-en-Laval
    34343 => 53, // Viols-le-Fort
];
$vdh = [
    34010 => 24, // Aniane
    34012 => 2, // Argelliers
    34016 => 1, // Aumelas
    34114 => 10, // Gignac
    34163 => 11, // Montarnaud
    34221 => 0, // Puéchabon
    34239 => 5, // Saint-André-de-Sangonis
];
$sete = [
    34023 => 16, // Balaruc-les-Bains
    34024 => 6, // Balaruc-le-Vieux
    34039 => 14, // Bouzigues
    34108 => 77, // Frontignan
    34113 => 11, // Gigean
    34143 => 0, // Loupian
    34150 => 154, // Marseillan
    34157 => 57, // Mèze
    34159 => 42, // Mireval
    34165 => 27, // Montbazin
    34213 => 26, // Poussan
    34301 => 215, // Sète
    34333 => 7, // Vic-la-Gardiole
    34341 => 57, // Villeveyrac
];
$camargue = [
    30003 => 89, // Aigues-Mortes
    30133 => 103, // Grau-du-Roi
    30276 => 69, // Saint-Laurent-d'Aigouze
];
$pcam = [
    30006 => 53, // Aimargues
    30020 => 0, // Aubord
    30033 => 2, // Beauvoisin
    30059 => 51, // Le Cailar
    30341 => 91, // Vauvert
];
$lunel = [
    34033 => 77, // Boisseron
    34048 => 0, // Campagne
    34246 => 2, // Entre-Vignes
    34110 => 3, // Galargues
    34112 => 0, // Garrigues
    34145 => 87, // Lunel
    34146 => 11, // Lunel-Viel
    34151 => 15, // Marsillargues
    34272 => 5, // Saint-Just
    34280 => 1, // Saint-Nazaire-de-Pézan
    34288 => 2, // Saint-Sériès
    34294 => 0, // Saturargues
    34296 => 17, // Saussines
    34340 => 2, // Villetelle
];
$insee = $m3m + $pdlor + $ccgpsl + $vdh + $sete + $camargue + $pcam + $lunel;
$items = [];
$classement = [];
if(count($insee) > 0){
    $date = filemtime(__DIR__.'/barometre.json');
    if(((time() - $date) / 60) > 5){
        $handler = fopen(__DIR__.'/barometre.json', 'w+');
        $data = file_get_contents('https://www.barometre-velo.fr/stats/progress.geojson');
        fwrite($handler, $data);
        fclose($handler);
        $date = date('d/m/Y H:i:s');
    }else{
        $data = file_get_contents(__DIR__.'/barometre.json');
        $date = date('d/m/Y H:i:s', $date);
    }
    $data = @json_decode($data);
    if(is_object($data) && isset($data->features)){
        if(is_array($data->features) && count($data->features) > 0){
            foreach($data->features as $feature){
                if(is_object($feature) && isset($feature->properties) && is_object($feature->properties) && isset($feature->properties->insee)){
                    if(array_key_exists($feature->properties->insee, $insee)) {
                        $compare = '-';
                        $value21 = '<em>non qualifiée</em>';
                        if($insee[$feature->properties->insee] < 50){
                            $compare = '&#128077;';
                            if(!is_null($insee[$feature->properties->insee])){
                                $value21 = '<em>non qualifiée ('.$insee[$feature->properties->insee].')</em>';
                                if($feature->properties->contributions >= $insee[$feature->properties->insee]){
                                    $compare = '&#128077; (+'.((int) $feature->properties->contributions - (int)$insee[$feature->properties->insee]).')';
                                }else{
                                    $compare = '&#128078; ('.((int)$feature->properties->contributions - (int)$insee[$feature->properties->insee]).')';
                                }
                            }
                        }else{
                            if($feature->properties->contributions >= $insee[$feature->properties->insee]){
                                $compare = '&#128077; (+'.((int) $feature->properties->contributions - (int)$insee[$feature->properties->insee]).')';
                                $value21 = $insee[$feature->properties->insee];
                            }else{
                                $compare = '&#128078; ('.((int)$feature->properties->contributions - (int)$insee[$feature->properties->insee]).')';
                                $value21 = $insee[$feature->properties->insee];
                            }
                        }
                        $items[$feature->properties->insee] = [
                            'commune'       => $feature->properties->name,
                            'valeur'        => $feature->properties->contributions,
                            'compare'       => $compare,
                            'valeur2021'    => $value21,
                            'rawValeur2021' => $insee[$feature->properties->insee],
                            'is30'          => $feature->properties->population < 5000,
                            'population'    => $feature->properties->population,
                            'per_cent'      => $feature->properties->per_cent,
                        ];
                        $classement[$feature->properties->insee] = $feature->properties->contributions;
                    }
                }

            }
        }
    }
}
arsort($classement);
$tables = [
    'mpl'   => [
        'title'     => 'Montpellier 3M',
        'short'     => 'Montpellier',
        'message'   => 'En 2021, 26 communes avaient été qualifiées', //'En 2017 seules 3 communes s\'étaient qualifiées : Montpellier (1666), Castelnau-le-Lez (63) et Castries (53).',
        'data'      => array_keys($m3m),
    ],
    'pdlor'   => [
        'title'     => 'Pays de l\'Or',
        'short'     => 'Pays de l\'Or',
        'message'   => 'En 2021, 5 communes avaient été qualifiées', //'En 2017 une seule s\'était qualifiée : Mauguio (51).',
        'data'      => array_keys($pdlor),
    ],
    'ccgpsl'   => [
        'title'     => 'Grand-Pic-Saint-Loup',
        'short'     => 'CCGPSL',
        'message'   => 'Seules les communes proches de Montpellier ou qualifiées en 2019 ou 2021 sont listées.', //<br>Aucune commune ne s\'était qualifiée en 2017.',
        'data'      => array_keys($ccgpsl),
    ],
    'vdh'   => [
        'title'     => 'Vallée de l\'Hérault',
        'short'     => 'Val Hérault',
        'message'   => 'Seules les communes proches de Montpellier ou qualifiées en 2019 ou 2021 sont listées.', //<br>Aucune commune ne s\'était qualifiée en 2017.',
        'data'      => array_keys($vdh),
    ],
    'sete'   => [
        'title'     => 'Sète Agglopôle',
        'short'     => 'Sète AM',
        'message'   => '', //'En 2017, seules 4 communes s\'étaient qualifiées : Sète (272), Frontignan (127), Marseillan (83) et Mèze (72).',
        'data'      => array_keys($sete),
    ],
    'lunel'   => [
        'title'     => 'Pays de Lunel',
        'short'     => 'Lunel',
        'message'   => '', //"Aucune commune ne s'était qualifiée en 2017.",
        'data'      => array_keys($lunel),
    ],
    'camargue'   => [
        'title'     => 'Terre de Camargue',
        'short'     => 'T. de Camargue',
        'message'   => '', //"Aucune commune ne s'était qualifiée en 2017.",
        'data'      => array_keys($camargue),
    ],
    'pcam'   => [
        'title'     => 'Petite Carmargue',
        'short'     => 'P. Camargue',
        'message'   =>'', // "Aucune commune ne s'était qualifiée en 2017.",
        'data'      => array_keys($pcam),
    ],
];
$values2019 = [
    'mpl' => [
        34172 => 3719, // Montpellier
        34022 => 12, // Baillargues
        34027 => 1, // Beaulieu
        34057 => 207, // Castelnau
        34058 => 29, // Castries
        34077 => 121, // Clapiers
        34090 => 60, // Le Crès
        34087 => 1, // Cournonsec
        34088 => 1, // Cournonterral
        34095 => 5, // Fabrègues
        34116 => 73, // Grabels
        34120 => 65, // Jacou
        34123 => 51, // Juvignac
        34129 => 63, // Lattes
        34134 => 6, // Lavérune
        34164 => 0, // Montaud
        34169 => 71, // Montferrier
        34179 => 5, // Murviel
        34198 => 12, // Pérols
        34202 => 22, // Pignan
        34217 => 124, // Prades
        34227 => 1, // Restinclières
        34244 => 4, // Saint-Brès
        34249 => 9, // Saint-Drézéry
        34256 => 1, // Saint-Geniès
        34259 => 66, // Saint-Georges
        34270 => 71, // Saint-Jean-de-Védas
        34295 => 1, // Saussan
        34307 => 2, // Sussargues
        34327 => 36, // Vendargues
        34337 => 6, // Villeneuve
    ],
    'pdlor' => [
        34154 => 77, // Mauguio
        34050 => 1, // Candillargues
        34127 => 1, // Lansargues
        34344 => 8, // La Grande-Motte
        34176 => 1, // Mudaison
        34192 => 15, // Palavas
        34240 => 55, // Saint-Aunes
        34321 => 0, // Valergues
    ],
    'ccgpsl' => [
        34014 => 8, // Assas
        34078 => 1, // Claret
        34082 => 21, // Combaillaux
        34102 => 0, // Fontanès
        34118 => 1, // Guzargues
        34131 => 0, // Lauret
        34152 => 10, // Mas-de-Londres
        34153 => 51, // Les Matelles
        34177 => 0, // Murles
        34185 => 3, // ND Londres
        34242 => 4, // Saint-Bauzille
        34247 => 0, // Saint-Clement
        34255 => 81, // Saint-Gély-du-Fesc
        34266 => 7, // Saint-Jean-de-Cuculles
        34274 => 60, // Saint-Martin
        34276 => 76, // Saint-Mathieu
        34290 => 6, // Saint-Vincent
        34248 => 7, // Sainte-Croix
        34309 => 18, // Teyran
        34314 => 0, // Le Triadou
        34320 => 13, // Vailhauquès
        34322 => 2, // Valflaunès
        34342 => 4, // Viols-en-Laval
        34343 => 33, // Viols-le-Fort
    ],
    'vdh' => [
        34010 => 8, // Aniane
        34012 => 1, // Argelliers
        34016 => 1, // Aumelas
        34114 => 9, // Gignac
        34163 => 9, // Montarnaud
        34221 => 0, // Puéchabon
        34239 => 0, // Saint-André-de-Sangonis
    ],
    'sete' => [
        34023 => 7, // Balaruc-les-Bains
        34024 => 1, // Balaruc-le-Vieux
        34039 => 1, // Bouzigues
        34108 => 83, // Frontignan
        34113 => 4, // Gigean
        34143 => 2, // Loupian
        34150 => 186, // Marseillan
        34157 => 28, // Mèze
        34159 => 2, // Mireval
        34165 => 3, // Montbazin
        34213 => 6, // Poussan
        34301 => 216, // Sète
        34333 => 6, // Vic-la-Gardiole
        34341 => 0, // Villeveyrac
    ],
    'camargue' => [
        30003 => 4, // Aigues-Mortes
        30133 => 10, // Grau-du-Roi
        30276 => 0, // Saint-Laurent-d'Aigouze
    ],
    'pcam' => [
        30006 => 0, // Aimargues
        30020 => 0, // Aubord
        30033 => 1, // Beauvoisin
        30059 => 1, // Le Cailar
        30341 => 1, // Vauvert
    ],
    'lunel' => [
        34033 => 0, // Boisseron
        34048 => 0, // Campagne
        34246 => 0, // Entre-Vignes
        34110 => 0, // Galargues
        34112 => 0, // Garrigues
        34145 => 18, // Lunel
        34146 => 0, // Lunel-Viel
        34151 => 1, // Marsillargues
        34272 => 0, // Saint-Just
        34280 => 0, // Saint-Nazaire-de-Pézan
        34288 => 0, // Saint-Sériès
        34294 => 0, // Saturargues
        34296 => 0, // Saussines
        34340 => 0, // Villetelle
    ],
];
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Comparaisons sur le baromètre des villes cyclables 2025 vs 2021 et 2019</title>
  </head>
 <body>
    <nav class="navbar sticky-top navbar-expand-sm navbar-dark bg-dark">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php foreach($tables as $key => $table): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#<?php echo $key ?>"><?php echo $table['title'] ?></a>
                    </li>
                <?php endforeach ?>
                <li class="nav-item">
                    <a class="nav-link" href="#classement">Classement</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1>Comparaisons sur le baromètre des villes cyclables 2025</h1>
        <div class="alert alert-info">
            <?php echo $countdown ?>
            Une commune de + de 5 000 habitants doit obtenir au moins 50 réponses pour être qualifiée, 30 sinon.
        </div>
         <?php foreach($tables as $key => $table): ?>
             <div id="<?php echo $key ?>" style="height:55px">&nbsp;</div>
             <h2><?php echo $table['title'] ?></h2>
             <?php if(strlen($table['message']) > 0): ?>
                <p class="alert alert-info"><?php echo $table['message'] ?></p>
            <?php endif ?>
             <table class="table  table-hover">
               <thead>
                   <tr>
                       <th>Commune</th>
                       <th>Valeur</th>
                       <th>&#128077;/&#128078; (vs 2021)</th>
                       <th>Valeur 2021</th>
                       <th>Valeur 2019</th>
                       <th><abbr title="Pourcentage de la population 2018">% pop.</abbr></th>
                   </tr>
               </thead>
               <tbody>
                    <?php $qualif = 0; $villes = 0; $totc2c = 0; ?>
                   <?php foreach($table['data'] as $insee): ?>
                        <?php $threshold = $items[$insee]['is30'] ? 30 : 50; ?>
                        <?php $hotThreshold = $items[$insee]['is30'] ? 25 : 40; ?>
                       <?php if(isset($items[$insee])): ?>
                            <?php if($items[$insee]['valeur'] >= $threshold) { $qualif++; } ?>
                           <tr <?php if($items[$insee]['valeur'] >= $threshold): ?>class="table-success"<?php elseif($items[$insee]['valeur'] >= $hotThreshold): ?>class="table-warning"<?php endif ?>>
                               <td><?php echo $items[$insee]['commune'] ?><?php if($items[$insee]['is30']) :?><sup>*</sup><?php endif ?></td>
                               <td><?php echo $items[$insee]['valeur'] ?></td>
                               <td><?php echo $items[$insee]['compare'] ?></td>
                               <td><?php echo $items[$insee]['valeur2021'] ?></td>
                               <td><?php echo $values2019[$key][$insee] > 50 ? $values2019[$key][$insee] : "<em>non qualifiée (".$values2019[$key][$insee].")</em>" ?></td>
                               <td><?php echo $items[$insee]['per_cent'] ?></td>
                           </tr>
                       <?php endif ?>
                        <?php $villes++; ?>
                        <?php $totc2c += $items[$insee]['valeur'] ?>
                    <?php endforeach ?>
               </tbody>
               <?php if($villes > 0): ?>
                <tfoot>
                    <tr>
                        <th colspan="5">
                            Communes qualifiées : <?php echo $qualif ?>/<?php echo $villes ?> (sur les communes observées) ; réponses sur l'EPCI : <?php echo $totc2c ?>
                        </th>
                    </tr>
               </tfoot>
               <?php endif ?>
           </table>
       <?php endforeach ?>
        <p><em><sup>*</sup> commune de moins de 5 000 habitants</p>
            <div id="classement" style="height:55px">&nbsp;</div>
            <h2>Classement</h2>
        <table class="table  table-hover">
            <thead>
                <tr><th>#</th><th>Commune</th><th>Valeur</th><th><abbr title="réponses manquantes">Rép. manq.</abbr></th><th>%age de 2021</tr>
            </thead>
            <tbody>
                <?php $i = 1 ?>
                <?php foreach($classement as $insee => $v): ?>
                    <?php $threshold = $items[$insee]['is30'] ? 30 : 50; ?>
                    <?php $hotThreshold = $items[$insee]['is30'] ? 25 : 40; ?>
                    <?php $val2021 =  ($items[$insee]['rawValeur2021'] > 0) ? ceil(($items[$insee]['valeur'] / $items[$insee]['rawValeur2021']) * 100) : null; ?>
                    <?php $valProgress = min($val2021, 100) ?>
                    <?php $color = $valProgress == 100 ? 'bg-success' : ($valProgress > 50 ? 'bg-info' : ($valProgress > 25 ? 'bg-warning' : 'bg-danger')) ?>
                    <?php $thProgress = min(($items[$insee]['valeur'] / $threshold) * 100, 100) ?>
                    <?php $thColor = $thProgress == 100 ? 'bg-success' : ($thProgress > 50 ? 'bg-info' : ($thProgress > 25 ? 'bg-warning' : 'bg-danger')) ?>
                    <tr <?php if($items[$insee]['valeur'] >= $threshold): ?>class="table-success"<?php elseif($items[$insee]['valeur'] >= $hotThreshold): ?>class="table-warning"<?php endif ?>>
                        <td><?php echo $i++ ?></td>
                        <td><?php echo $items[$insee]['commune'] ?><?php if($items[$insee]['is30']) :?><sup>*</sup><?php endif ?></td>
                        <td><?php echo $items[$insee]['valeur'] ?></td>
                        <td class="center text-center">
                                <?php echo max(0, $threshold - $items[$insee]['valeur']) ?>
                            <div class="progress" style="height: 2px">
                                <div class="progress-bar <?php echo $thColor ?>" role="progressbar" aria-label="" style="width: <?php echo $thProgress ?>%" aria-valuenow="<?php echo $thProgress ?>" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </td>
                        <td style="position:relative">
                            <?php if(is_null($val2021)): ?>
                                aucune rép. 2021
                            <?php else: ?>
                                <?php if (!is_null($thresholdDays)): ?>
                                    <div style="position: absolute; border: 0; border-left: 1px dotted red;left: <?php echo $thresholdDays ?>%;top: 5px;">&nbsp;</div>
                                <?php endif ?>
                                <div class="progress">
                                    <div class="progress-bar <?php echo $color ?>" role="progressbar" aria-label="" style="width: <?php echo $valProgress ?>%" aria-valuenow="<?php echo $valProgress ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $val2021 ?>&nbsp;%</div>
                                </div>
                            <?php endif ?>
                        </th>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <p><em><sup>*</sup> commune de moins de 5 000 habitants</p>
        <p class="alert alert-secondary">
            Date de dernière mise à jour : <?php echo $date ?>.<br>
            Pour éviter de se faire bannir par la plateforme Parlons-Vélo, la mise à jour se fait de manière périodique.
            Les données 2019 sont celles de <a href="https://public.tableau.com/app/profile/fub4080/viz/Patricipation2019_15682251368940/Participation_1" target="_blank">ce tableau</a>, en regardant les réponses de cyclistes uniquement.
        </p>
   </div>
</body>
</html>
