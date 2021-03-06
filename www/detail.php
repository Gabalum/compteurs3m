<?php
namespace App;
require_once('../bootstrap.php');
$slug = (isset($_GET['cpt']) ? strip_tags($_GET['cpt']) : '');
$compteur = (Compteurs::getInstance())->getCompteurBySlug($slug);
$compteurs = (Compteurs::getInstance())->getCompteurs();
if(is_null($compteur)){
    header('HTTP/1.0 404 Not Found');
    require_once('./404.php');
    exit;
}
$title = $compteur->get('label')." | Les compteurs de Montpellier 3M";
$desc = 'Détail du compteur '.$compteur->get('label').'. Découvrez les compteurs vélos grâce aux données en Open Data de Montpellier 3M';
$days = date('z')+1;
if(file_exists(__DIR__.'/assets/img/'.$compteur->get('id').'.jpg')){
    $imgSocial = _BASE_URL_.'assets/img/'.$compteur->get('id').'.jpg';
}else{
    $imgSocial = _BASE_URL_.'assets/img/albert-fb.jpg';
}
?><!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?></title>
    <meta property="og:locale" content="fr_FR" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $title ?>" />
    <meta property="og:description" content="<?php echo $desc ?>" />
    <meta property="og:url" content="<?php echo _BASE_URL_ ?>" />
    <meta property="og:site_name" content="<?php echo $title ?>" />
    <meta property="og:image" content="<?php echo $imgSocial ?>" />
    <meta property="og:image:secure_url" content="<?php echo $imgSocial ?>" />
    <meta property="og:image:width" content="1000" />
    <meta property="og:image:height" content="500" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="<?php echo _BASE_URL_ ?>" />
    <meta name="twitter:description" content="<?php echo $desc ?>" />
    <meta name="twitter:title" content="<?php echo $title ?>" />
    <meta name="twitter:image" content="<?php echo $imgSocial ?>" />
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <link type="text/css" rel="stylesheet" href="<?php echo _BASE_URL_.Helper::noCache('assets/css/main.css') ?>" media="all" />
    <link type="text/css" rel="stylesheet" href="<?php echo _BASE_URL_.Helper::noCache('assets/css/weather-icons.min.css') ?>" media="all" />
	<link rel="apple-touch-icon" sizes="90x90" href="<?php echo _BASE_URL_ ?>assets/img/favicons/favicon.png">
	<link rel="icon" type="image/x-icon" sizes="90x90" href="<?php echo _BASE_URL_ ?>assets/img/favicons/favicon.png">
</head>
<body class="detail">
    <section id="head" class="container-lg">
        <h1><?php echo $compteur->get('labelHTML') ?></h1>
        <div class="float-end">
            <?php if($slug == 'albert-1er'): ?>
                <a class="btn btn-totem" href="<?php echo _BASE_URL_ ?>communautaire/albert">
                    Les données communautaires
                </a>
            <?php endif ?>
            <a class="btn btn-totem" href="<?php echo _BASE_URL_ ?>">
                Tous les compteurs
            </a>
        </div>
        <p>Dernier relevé : <?php echo str_replace('-', '/', $compteur->get('lastDate')) ?></p>
    </section>
    <section id="main" class="container-lg">
        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="card card-init">
                    <div class="card-body">
                        <div class="card-title">
                            <h6><?php echo $compteur->get('labelHTML') ?></h6>
                            <address><?php echo $compteur->get('address') ?></address>
                        </div>
                        <?php if(file_exists(__DIR__.'/assets/img/'.$compteur->get('id').'.jpg')): ?>
                            <img class="photo-detail" src="<?php echo _BASE_URL_.'assets/img/'.$compteur->get('id').'.jpg' ?>" />
                        <?php endif ?>
                        <div class="map detail" id="map-<?php echo $compteur->get('id') ?>" data-id="<?php echo $compteur->get('id') ?>" data-lat="<?php echo $compteur->get('lat') ?>" data-lng="<?php echo $compteur->get('lng') ?>"></div>
                    </div>
                </div>
                <div class="ring card d-none d-sm-block">
                    <div class="card-body">
                        <div class="card-title">
                            <h6>Accès aux stats :</h6>
                        </div>
                        <?php if(count($compteurs) > 0): ?>
                            <div class="list-group list-group-flush">
                                    <a href="<?php echo _BASE_URL_ ?>" class="nav-link list-group-item list-group-item-action list-group-item-light">
                                        Toutes les données
                                    </a>
                                <?php foreach($compteurs as $cpt): ?>
                                    <a href="<?php echo _BASE_URL_.'detail/'.$cpt->get('slug') ?>" class="nav-link list-group-item list-group-item-action list-group-item-light <?php echo ($cpt->get('slug') == $slug ? 'active' : '') ?>">
                                        <?php echo $cpt->get('labelHTML') ?>
                                    </a>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-8 detail-data-col">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <h6>Passages par jour</h6>
                            <em>En <?php echo date('Y') ?></em>
                        </div>
                        <canvas id="bar-day-<?php echo $compteur->get('id') ?>" class="bar-detail bar-day" data-labels='<?php echo json_encode($compteur->get('chartDates', $days)) ?>' data-values='<?php echo json_encode($compteur->get('chartData', $days)) ?>'></canvas>
                    </div>
                </div>
                <?php $days = $compteur->get('days-by-year') ?>
                <?php if(count($days) > 0 && isset($days[date('Y')]) && count($days[date('Y')]) > 0): ?>
                    <?php $days = $days[date('Y')] ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h6>Records par jour de la semaine</h6>
                                <em>En <?php echo date('Y') ?></em>
                            </div>
                            <canvas id="bar-day2-<?php echo $compteur->get('id') ?>" class="bar-detail bar-days2 records" data-labels='<?php echo json_encode(array_map(Helper::class.'::frenchDayOfTheWeek', array_keys($days))) ?>' data-values='<?php echo json_encode(array_column($days, 'value')) ?>'></canvas>
                        </div>
                    </div>
                <?php endif ?>
                <?php $days = $compteur->get('days-by-year') ?>
                <?php if(count($days) > 0 && isset($days[date('Y')]) && count($days[date('Y')]) > 0): ?>
                    <?php $days = $days[date('Y')] ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h6>Moyennes par jour de la semaine</h6>
                                <em>En <?php echo date('Y') ?>, calculées en fonction des données disponibles (certains jours peuvent être manquants)</em>
                            </div>
                            <canvas id="bar-day3-<?php echo $compteur->get('id') ?>" class="bar-detail bar-days3 records" data-labels='<?php echo json_encode(array_map(Helper::class.'::frenchDayOfTheWeek', array_keys($days))) ?>' data-values='<?php echo json_encode(array_column($days, 'avg')) ?>'></canvas>
                        </div>
                    </div>
                <?php endif ?>
                <?php $weeks = $compteur->get('weeks') ?>
                <?php if(count($weeks) > 0): ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <h6>Passages par semaine</h6>
                                <em>En <?php echo date('Y') ?>, total en fonction des données disponibles (certains jours peuvent être manquants)</em>
                            </div>
                            <canvas id="bar-weeks-<?php echo $compteur->get('id') ?>" class="bar-detail bar-weeks" data-labels='<?php echo json_encode(array_keys($weeks)) ?>' data-values='<?php echo json_encode(array_column($weeks, 'sum')) ?>'></canvas>
                        </div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </section>
    <div class="container-lg  d-block d-sm-none">
        <div class="ring card">
            <div class="card-body">
                <div class="card-title">
                    <h6>Accès aux stats :</h6>
                </div>
                <?php if(count($compteurs) > 0): ?>
                    <div class="list-group list-group-flush">
                            <a href="<?php echo _BASE_URL_ ?>" class="nav-link list-group-item list-group-item-action list-group-item-light">
                                Toutes les données
                            </a>
                        <?php foreach($compteurs as $cpt): ?>
                            <a href="<?php echo _BASE_URL_.'detail/'.$cpt->get('slug') ?>" class="nav-link list-group-item list-group-item-action list-group-item-light <?php echo ($cpt->get('slug') == $slug ? 'active' : '') ?>">
                                <?php echo $cpt->get('labelHTML') ?>
                            </a>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
    <footer>
        <div class="container">
            <?php require_once('./parts/note-footer.php') ?>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <?php /* ?><script src="https://cdn.jsdelivr.net/npm/chartjs-chart-matrix@0.1.3"></script><?php /* */ ?>
    <script src="<?php echo _BASE_URL_.Helper::noCache('./assets/js/main.js') ?>"></script>
    <?php require_once('./parts/matomo.php') ?>
</body>
</html>
