<?php

function sortCmp($a, $b){
    return ($a['valeur'] <= $b['valeur'] ? -1 : 1);
}
$date = filemtime(__DIR__.'/barometre.json');
if(((time() - $date) / 60) > 10){
    $handler = fopen(__DIR__.'/barometre.json', 'w+');
    $data = file_get_contents('https://barometre.parlons-velo.fr/api/4cds56c4sdc4c56ds4cre84c13ez8c4ezc6eza9c84ze16464cdsc1591cdzf8ez/stats/geojson');
    fwrite($handler, $data);
    fclose($handler);
}else{
    $data = file_get_contents(__DIR__.'/barometre.json');
}
$data = @json_decode($data);
$insee = [
    59183 => 'Dunkerque',
    62193 => 'Calais',
    59350 => 'Lille',
    59632 => 'Wallers (Arenberg)',
    54323 => 'Longwy',
    54526 => 'Tomblaine',
    70414 => 'Planche des Belles-Filles (Plancher-les-Mines)',
    39198 => 'Dole',
    74063 => 'Châtel',
    74191 => 'Morzine',
    74173 => 'Megève',
    73011 => 'Albertville',
    '05161' => 'Col du Granon (La Salle-les-Alpes)',
    '05023' => 'Briançon',
    38191 => "Alpe-d'Huez (Huez)",
    38052 => "Le Bourg-d'Oisans",
    42218 => "Saint-Étienne",
    48095 => "Mende",
    12202 => 'Rodez',
    11069 => 'Carcassonne',
    '09122' => 'Foix',
    31483 => 'Saint-Gaudens',
    31221 => 'Peyragudes (Gouaux-de-Larboust)',
    65199 => 'Peyragudes (Germ)',
    65286 => 'Lourdes',
    65077 => 'Hautacam (Beaucens)',
    65129 => 'Castelnau-Magnoac',
    46042 => 'Cahors',
    46143 => 'Lacapelle-Marival',
    46240 => "Rocamadour",
    92050 => 'Paris La Défense Arena (Nanterre)',
    75056 => 'Paris Champs Élysées (Paris)',
];
$items = [];
$inseeKeys = array_keys($insee);
if(is_object($data) && isset($data->features)){
    if(is_array($data->features) && count($data->features) > 0){
        foreach($data->features as $feature){
            if(is_object($feature) && isset($feature->properties) && is_object($feature->properties) && isset($feature->properties->insee)){
                if(in_array($feature->properties->insee, $inseeKeys)){
                    $items[$feature->properties->insee] = [
                        'commune'   => $insee[$feature->properties->insee],
                        'valeur'    => $feature->properties->contributions,
                    ];
                }
            }

        }
    }
}
foreach($insee as $k => $v){
    if(!isset($items[$k])){
        $items[$k]  = [
            'commune'   => $v,
            'valeur'    => 0,
        ];
    }
}
usort($items, 'sortCmp');
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Baromètre et Tour de France</title>
  </head>
 <body>
     <div class="container">
         <h1>Le baromètre et les villes du Tour de France</h1>
         <p>Avec sa campagne <a href="https://www.letour.fr/fr/nos-engagements/l-avenir-a-velo" target="_blank">L'Avenir à Vélo</a> le Tour de France s'engage dans la mobilité à vélo.<br>
             Mais qu'en est-il des villes étapes ?</p>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Commune</th>
                    <th>Valeur</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                    <tr <?php if($item['valeur'] >= 50): ?>class="table-success"<?php elseif($item['valeur'] >= 40): ?>class="table-warning"<?php endif ?>>
                        <td><?php echo $item['commune'] ?></td>
                        <td><?php echo $item['valeur'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
