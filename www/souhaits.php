<?php
namespace App;
require_once('../bootstrap.php');
    $coords = [
        [43.621518140660555, 3.9104652439597913, true], // Castelnau - G. Frêche
        [43.6280822682752, 3.904464152400085, true], // Castelnau - chemin de Verchant
        [43.64610981562929, 3.8096421893914685, true], // Grabels - entrée
        [43.594357097537284, 3.9036943197421294, true], // Montpellier - marché du Lez
        [43.570354693395025, 3.8980439554033715, true], // Lattes - piste vers Palavas
        [43.66104277933736, 3.9184616825036507, true], // Jacou - av. de Vendargues
        [43.65852673169794, 3.9566396702014277, true], // Vendargues - Entrepôt U 1
        [43.658862333532504, 3.9557120513123776, true], // Vendargues - Entrepôt U 2
        [43.59452822576315, 3.8970746250859043, true], // Montpellier - Aiguerelles
        [43.57210073790041, 3.835524427009353, true], // Saint-Jean-de-Védas - Clinique Saint-Jean
        [43.60630278101448, 3.863978609630855, true], // Montpellier - Figuerolles
        [43.624721891074365, 3.8274728529328477, true], // Montpellier - Pierrevives
        [43.65353216869659, 3.8846383829998374, true], // Clapiers - Liberté
        [43.658084146837545, 3.8508503005040615, true], // Montferrier - Lironde
    ];
    $compteurs = (Compteurs::getInstance())->getCompteurs();
    if(count($compteurs) > 0){
        foreach($compteurs as $c){
            $coords[] = [$c->get('lat'), $c->get('lng')];
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#000000">
    <meta name="description" content="">
    <title>Suggestions de compteurs</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <link rel="stylesheet" href="https://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-locatecontrol/v0.43.0/L.Control.Locate.css">
    <link type="text/css" rel="stylesheet" href="<?php echo _BASE_URL_.Helper::noCache('assets/css/main.css') ?>" media="all" />
    <link type="text/css" rel="stylesheet" href="<?php echo _BASE_URL_.Helper::noCache('assets/css/weather-icons.min.css') ?>" media="all" />
    <link rel="apple-touch-icon" sizes="90x90" href="<?php echo _BASE_URL_ ?>assets/img/favicons/favicon.png">
    <link rel="icon" type="image/x-icon" sizes="90x90" href="<?php echo _BASE_URL_ ?>assets/img/favicons/favicon.png">
    <style>
        #legend-box{
            font-size: 0.8em;
            width: 220px;
            padding: 0.5em;
            background: white;
            color: #000;
            position: absolute;
            right: 15px;
            bottom: 15px;
            z-index:9999;
            text-align: left;
        }
        .cpt{
            width: 22px;
            height: 28px;
        }
    </style>
</head>
<body>
    <section id="main-map" class="text-center align-middle container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div id="map" class="map-total map-fh" data-pins='<?php echo json_encode($coords) ?>' data-center="<?php echo json_encode([43.61388552482668, 3.8759991901419455]) ?>" style="width:100%; height:100%; min-height: 100%"></div>
            </div>
            <?php /* ?>
            <div class="col-md-3">
                <div id="content">
            </div>
            <?php /* */ ?>
        </div>
        <ul id="legend-box" class="list-group">
            <li class="list-group-item"><img src="/assets/img/markers/3d8bfd.png" class="cpt"> Compteur actuel</li>
            <li class="list-group-item"><img src="/assets/img/markers/8540f5.png" class="cpt"> Compteur suggéré</li>
        </ul>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script src="<?php echo _BASE_URL_.Helper::noCache('./assets/js/main.js') ?>"></script>
    <script type="text/javascript">
    </script>
    <?php require_once('./parts/matomo.php') ?>
</body>
</html>
