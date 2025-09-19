<?php
function sortCmp($a, $b){
    return ($a['valeur'] > $b['valeur'] ? -1 : 1);
}
$date = filemtime(__DIR__.'/barometre.json');
if(((time() - $date) / 60) > 10){
    $handler = fopen(__DIR__.'/barometre.json', 'w+');
    $data = file_get_contents('https://www.barometre-velo.fr/stats/progress.geojson');
    fwrite($handler, $data);
    fclose($handler);
}else{
    $data = file_get_contents(__DIR__.'/barometre.json');
}
$data = @json_decode($data);
$insee = [
    34172, // Montpellier
    34022, // Baillargues
    34027, // Beaulieu
    34057, // Castelnau
    34058, // Castries
    34077, // Clapiers
    34090, // Le Crès
    34087, // Cournonsec
    34088, // Cournonterral
    34095, // Fabrègues
    34116, // Grabels
    34120, // Jacou
    34123, // Juvignac
    34129, // Lattes
    34134, // Lavérune
    34164, // Montaud
    34169, // Montferrier
    34179, // Murviels
    34198, // Pérols
    34202, // Pignan
    34217, // Prades
    34227, // Restinclières
    34244, // Saint-Brès
    34249, // Saint-Drézéry
    34256, // Saint-Geniès
    34259, // Saint-Georges
    34270, // Saint-Jean-de-Védas
    34295, // Saussan
    34307, // Sussargues
    34327, // Vendargues
    34337, // Villeneuve
];
$secteurs = ["montpellier", "autre", "cadoule", "littoral", "piemont", "plaine", "vallee",];
$details = ((isset($_POST) && count($_POST) > 0 && isset($_POST['text']) && strlen($_POST['text']) > 0) || (count($_GET) > 0 && isset($_GET['text']) && strlen($_GET['text']) > 0));
$secteur = null;
if($details){
    $secteur = '';
    if(count($_POST) > 0 && isset($_POST['text']) && strlen($_POST['text'])){
        $secteur = strtolower(trim($_POST['text']));
    }elseif(count($_GET) > 0 && isset($_GET['text']) && strlen($_GET['text'])) {
        $secteur = strtolower(trim($_GET['text']));
    }
    if($secteur == 'montpellier'){
        $secteur = 'Montpellier';
        $insee = [34172];
    }elseif($secteur == 'cadoule'){
        $secteur = 'Cadoule et Bérange';
        $insee = [34327, 34022, 34058, 34244, 34249, 34164, 34027, 34307, 34227, 34256];
    }elseif($secteur == 'littoral'){
        $secteur = 'Littoral';
        $insee = [34337, 34198, 34129];
    }elseif($secteur == 'piemont' || $secteur == 'garrigues'){
        $secteur = 'Piémont et Garrigues';
        $insee = [34116, 34123, 34179, 34259];
    }elseif($secteur == 'plaine' || $secteur == 'ouest' || $secteur == 'plaineouest'){
        $secteur = 'Plaine Ouest';
        $insee = [34270, 34095, 34134, 34202, 34295, 34087, 34088];
    }elseif($secteur == 'vallee' ||$secteur == 'vallée' ||$secteur == 'valee'){
        $secteur = 'Vallée du Lez';
        $insee = [34057, 34077, 34090, 34120, 34169, 34217];
    }elseif($secteur == 'autre'){
        $secteur = 'hors 3M';
        $insee = [
            34154, // Mauguio
            34192, // Palavas
            34344, // Grande-Motte
            34255, // Saint-Gély-du-Fesc
            34247, // Saint-Clément-de-Rivière
            34309, // Teyran
            34014, // Assas
            34082, // Combaillaux
            34153, // Les Matelles
            34240, // Saint-Aunes
        ];
    }elseif($secteur == 'pic' || $secteur == 'ccgpsl'){
        $secteur = 'hors 3M / Grand-Pic-Saint-Loup';
        $insee = [
            34255, // Saint-Gély-du-Fesc
            34247, // Saint-Clément-de-Rivière
            34309, // Teyran
            34014, // Assas
            34082, // Combaillaux
            34153, // Les Matelles
        ];
    }elseif($secteur == 'or'){
        $secteur = 'hors 3M / Pays de l\'Or';
        $insee = [
            34154, // Mauguio
            34192, // Palavas
            34344, // Grande-Motte
            34240, // Saint-Aunes
        ];
    }else{
        $secteur = null;
    }
}
$mtp = [];
if(is_object($data) && isset($data->features)){
    if(is_array($data->features) && count($data->features) > 0){
        foreach($data->features as $feature){
            if(is_object($feature) && isset($feature->properties) && is_object($feature->properties) && isset($feature->properties->insee)){
                if(in_array($feature->properties->insee, $insee)){
                    $mtp[] = [
                        'commune'   => $feature->properties->name,
                        'valeur'    => $feature->properties->contributions,
                    ];
                }
            }

        }
    }
}
usort($mtp, 'sortCmp');
if(count($mtp) > 0){
    $message = 'Voila les dernières données du baromètre dont je dispose'.(!is_null($secteur) ? ' pour le secteur '.$secteur : '').' : '.PHP_EOL;
    foreach($mtp as $item){
        $message .= $item['commune'].' : '.$item['valeur'].PHP_EOL;
    }
    $message .= "N'oubliez pas, il faut 50 réponses (30 si moins de 5000 habitants) pour qu'une commune soit \"qualifiée\"".PHP_EOL;
}else{
    $message = 'Désolé, je n\'ai aucune valeur à communiquer pour le moment'.PHP_EOL;
}
header('Content-type: application/json');
echo json_encode([
	'response_type' => 'in_channel',
	'text'			=> $message,
]);
