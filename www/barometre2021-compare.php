<?php
function sortCmp($a, $b){
    return ($a['valeur'] <= $b['valeur'] ? -1 : 1);
}
$m3m = [
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
];
$pdlor = [
    34154 => 77, // Mauguio
    34050 => 1, // Candillargues
    34344 => 8, // La Grande-Motte
    34127 => 1, // Lansargues
    34176 => 1, // Mudaison
    34192 => 15, // Palavas
    34240 => 55, // Saint-Aunes
    34321 => 0, // Valergues
];
$ccgpsl = [
    34014 => 8, // Assas
    34078 => 1, // Claret
    34082 => 21, // Combaillaux
    34118 => 1, // Guzargues
    34152 => 10, // Mas-de-Londres
    34153 => 51, // Les Matelles
    34177 => 0, // Murles
    34185 => 3, // ND Londres
    34242 => 4, // Saint-Bauzille
    34247 => 0, // Saint-Clément
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
];
$vdh = [
    34010 => 8, // Aniane
    34012 => 1, // Argelliers
    34016 => 1, // Aumelas
    34114 => 9, // Gignac
    34163 => 9, // Montarnaud
];
$sete = [
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
];
$camargue = [
    30003 => 4, // Aigues-Mortes
    30133 => 10, // Grau-du-Roi
    30276 => 0, // Saint-Laurent-d'Aigouze
];
$pcam = [
    30006 => 0, // Aimargues
    30020 => 0, // Aubord
    30033 => 1, // Beauvoisin
    30059 => 1, // Le Cailar
    30341 => 1, // Vauvert
];
$lunel = [
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
];
$insee = $m3m + $pdlor + $ccgpsl + $vdh + $sete + $camargue + $pcam + $lunel;
$items = [];
if(count($insee) > 0){
    $date = filemtime(__DIR__.'/barometre2021.json');
    if(((time() - $date) / 60) > 5){
        $handler = fopen(__DIR__.'/barometre2021.json', 'w+');
        $data = file_get_contents('https://barometre.parlons-velo.fr/api/4cds56c4sdc4c56ds4cre84c13ez8c4ezc6eza9c84ze16464cdsc1591cdzf8ez/stats/geojson');
        fwrite($handler, $data);
        fclose($handler);
        $date = date('d/m/Y H:i:s');
    }else{
        $data = file_get_contents(__DIR__.'/barometre2021.json');
        $date = date('d/m/Y H:i:s', $date);
    }
    $data = @json_decode($data);
    if(is_object($data) && isset($data->features)){
        if(is_array($data->features) && count($data->features) > 0){
            foreach($data->features as $feature){
                if(is_object($feature) && isset($feature->properties) && is_object($feature->properties) && isset($feature->properties->insee)){
                    if(array_key_exists($feature->properties->insee, $insee)) {
                        $compare = '-';
                        $value19 = '<em>non qualifiée</em>';
                        if($insee[$feature->properties->insee] < 50){
                            $compare = '&#128077;';
                            if(!is_null($insee[$feature->properties->insee])){
                                $value19 = '<em>non qualifiée ('.$insee[$feature->properties->insee].')</em>';
                                if($feature->properties->contributions >= $insee[$feature->properties->insee]){
                                    $compare = '&#128077; (+'.((int) $feature->properties->contributions - (int)$insee[$feature->properties->insee]).')';
                                }else{
                                    $compare = '&#128078; ('.((int)$feature->properties->contributions - (int)$insee[$feature->properties->insee]).')';
                                }
                            }
                        }else{
                            if($feature->properties->contributions >= $insee[$feature->properties->insee]){
                                $compare = '&#128077; (+'.((int) $feature->properties->contributions - (int)$insee[$feature->properties->insee]).')';
                                $value19 = $insee[$feature->properties->insee];
                            }else{
                                $compare = '&#128078; ('.((int)$feature->properties->contributions - (int)$insee[$feature->properties->insee]).')';
                                $value19 = $insee[$feature->properties->insee];
                            }
                        }
                        $items[$feature->properties->insee] = [
                            'commune'       => $feature->properties->name,
                            'valeur'        => $feature->properties->contributions,
                            'compare'       => $compare,
                            'valeur2019'    => $value19,
                        ];
                    }
                }

            }
        }
    }
}
$tables = [
    'mpl'   => [
        'title'     => 'Montpellier 3M',
        'short'     => 'Montpellier',
        'message'   => 'En 2017 seules 3 communes s\'étaient qualifiées : Montpellier (1666), Castelnau-le-Lez (63) et Castries (53).',
        'data'      => array_keys($m3m),
    ],
    'pdlor'   => [
        'title'     => 'Pays de l\'Or',
        'short'     => 'Pays de l\'Or',
        'message'   => 'En 2017 une seule s\'était qualifiée : Mauguio (51).',
        'data'      => array_keys($pdlor),
    ],
    'ccgpsl'   => [
        'title'     => 'Grand-Pic-Saint-Loup',
        'short'     => 'CCGPSL',
        'message'   => 'Seules les communes proches de Montpellier ou qualifiées en 2019 ou 2021 sont listées.<br>Aucune commune ne s\'était qualifiée en 2017.',
        'data'      => array_keys($ccgpsl),
    ],
    'vdh'   => [
        'title'     => 'Vallée de l\'Hérault',
        'short'     => 'Val Hérault',
        'message'   => 'Seules les communes proches de Montpellier ou qualifiées en 2019 ou 2021 sont listées.<br>Aucune commune ne s\'était qualifiée en 2017.',
        'data'      => array_keys($vdh),
    ],
    'sete'   => [
        'title'     => 'Sète Agglopôle',
        'short'     => 'Sète AM',
        'message'   => 'En 2017, seules 4 communes s\'étaient qualifiées : Sète (272), Frontignan (127), Marseillan (83) et Mèze (72).',
        'data'      => array_keys($sete),
    ],
    'lunel'   => [
        'title'     => 'Pays de Lunel',
        'short'     => 'Lunel',
        'message'   => "Aucune commune ne s'était qualifiée en 2017.",
        'data'      => array_keys($lunel),
    ],
    'camargue'   => [
        'title'     => 'Terre de Camargue',
        'short'     => 'T. de Camargue',
        'message'   => "Aucune commune ne s'était qualifiée en 2017.",
        'data'      => array_keys($camargue),
    ],
    'pcam'   => [
        'title'     => 'Petite Carmargue',
        'short'     => 'P. Camargue',
        'message'   => "Aucune commune ne s'était qualifiée en 2017.",
        'data'      => array_keys($pcam),
    ],
];
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Comparaisons sur le baromètre des villes cyclables</title>
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
            </ul>
        </div>
    </nav>
     <div class="container">
         <h1>Comparaisons sur le baromètre des villes cyclables</h1>
         <?php foreach($tables as $key => $table): ?>
             <div id="<?php echo $key ?>" style="height:55px">&nbsp;</div>
             <h2><?php echo $table['title'] ?></h2>
             <p class="alert alert-info"><?php echo $table['message'] ?></p>
             <table class="table  table-hover">
               <thead>
                   <tr>
                       <th>Commune</th>
                       <th>Valeur</th>
                       <th>&#128077;/&#128078;</th>
                       <th>Valeur 2019</th>
                   </tr>
               </thead>
               <tbody>
                   <?php foreach($table['data'] as $insee): ?>
                       <?php if(isset($items[$insee])): ?>
                           <tr <?php if($items[$insee]['valeur'] >= 50): ?>class="table-success"<?php elseif($items[$insee]['valeur'] >= 40): ?>class="table-warning"<?php endif ?>>
                               <td><?php echo $items[$insee]['commune'] ?></td>
                               <td><?php echo $items[$insee]['valeur'] ?></td>
                               <td><?php echo $items[$insee]['compare'] ?></td>
                               <td><?php echo $items[$insee]['valeur2019'] ?></td>
                           </tr>
                       <?php endif ?>
                   <?php endforeach ?>
               </tbody>
           </table>
       <?php endforeach ?>
        <p class="alert alert-light">
            Date de dernière mise à jour : <?php echo $date ?>.<br>
            Pour éviter de se faire bannir par la plateforme Parlons-Vélo, la mise à jour se fait de manière périodique.
            Les données 2019 sont celles de <a href="https://public.tableau.com/app/profile/fub4080/viz/Patricipation2019_15682251368940/Participation_1" target="_blank">ce tableau</a>, en regardant les réponses de cyclistes uniquement.
        </p>
   </div>
</body>
</html>
