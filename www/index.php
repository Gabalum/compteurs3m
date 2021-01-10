<?php
namespace App;
require_once('../bootstrap.php');
$compteurs = (Compteurs::getInstance())->getCompteurs();
$baseUrl = 'https://compteurs.velocite-montpellier.fr/';
$title = 'Les compteurs vélos de Montpellier 3M';
$desc = 'Découvrez les compteurs vélos grâce aux données en Open Data de Montpellier 3M';
$days = 14;
$meteoData = (new Meteo())->getData($days);
$weatherData = (new Meteo())->getWeather($days);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title ?></title>
    <meta property="og:locale" content="fr_FR" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $title ?>" />
    <meta property="og:description" content="<?php echo $desc ?>" />
    <meta property="og:url" content="<?php echo $baseUrl ?>" />
    <meta property="og:site_name" content="<?php echo $title ?>" />
    <meta property="og:image" content="<?php echo $baseUrl ?>/assets/img/albert-fb.jpg" />
    <meta property="og:image:secure_url" content="<?php echo $baseUrl ?>/assets/img/albert-fb.jpg" />
    <meta property="og:image:width" content="1000" />
    <meta property="og:image:height" content="500" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="<?php echo $baseUrl ?>" />
    <meta name="twitter:description" content="<?php echo $desc ?>" />
    <meta name="twitter:title" content="<?php echo $title ?>" />
    <meta name="twitter:image" content="<?php echo $baseUrl ?>/assets/img/albert-fb.jpg" />

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <link type="text/css" rel="stylesheet" href="<?php echo Helper::noCache('./assets/css/main.css') ?>" media="all" />
    <link type="text/css" rel="stylesheet" href="<?php echo Helper::noCache('./assets/css/weather-icons.min.css') ?>" media="all" />
	<link rel="apple-touch-icon" sizes="90x90" href="./assets/img/favicons/favicon.png">
	<link rel="icon" type="image/x-icon" sizes="90x90" href="./assets/img/favicons/favicon.png">
</head>
<body>
    <nav role="navigation" id="bugerNav">
        <div id="menuToggle">
            <input type="checkbox" id="menuToggler" />
            <span></span>
            <span></span>
            <span></span>
            <ul id="menu">
                <?php if(count($compteurs) > 0): ?>
                    <?php foreach($compteurs as $k => $compteur): ?>
                        <li>
                            <a href="#compteur-<?php echo $compteur->get('slug') ?>">
                                <?php echo $compteur->get('labelHTML') ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                    <?php if(count($meteoData) > 0): ?>
                        <li>
                            <a href="#meteo-temperature">Météo</a>
                        </li>
                    <?php endif ?>
                <?php endif ?>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1>Les compteurs vélos de Montpellier 3M</h1>
        <?php if(count($compteurs) > 0): ?>
            <?php foreach($compteurs as $k => $compteur): ?>
                <section class="compteur" id="compteur-<?php echo $compteur->get('slug') ?>">
                    <h2>
                        <a href="#compteur-<?php echo $compteur->get('slug') ?>">
                            <?php echo $compteur->get('labelHTML') ?>
                        </a>
                    </h2>
                    <div class="row compteur-data">
                        <div class="col-4 data-col">
                            <div class="map" id="map-<?php echo $k ?>" data-id="<?php echo $k ?>" data-lat="<?php echo $compteur->get('lat') ?>" data-lng="<?php echo $compteur->get('lng') ?>"></div>
                        </div>
                        <div class="col-8 data-col data-col-2">
                            <div class="row">
                                <div class="col">
                                    <div class="card card-last <?php echo ($compteur->get('lastValue') == $compteur->get('recordTotal')?'light':'') ?>">
                                        <div class="card-body">
                                            <h5 class="card-title">Dernier relevé</h5>
                                            <p class="card-text cpt"><?php echo $compteur->get('lastValue') ?></p>
                                            <h6 class="card-subtitle mb-2 text-muted">Le <?php echo $compteur->get('lastDate') ?></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card card-record <?php echo ($compteur->get('lastValue') == $compteur->get('recordTotal')?'light':'') ?>">
                                        <div class="card-body">
                                            <h5 class="card-title">Record</h5>
                                            <p class="card-text cpt"><?php echo $compteur->get('recordTotal') ?></p>
                                            <h6 class="card-subtitle mb-2 text-muted">Le <?php echo $compteur->get('recordTotalDate') ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row sep">
                                <div class="col">
                                    <div class="card card-moy">
                                        <div class="card-body">
                                            <h5 class="card-title">Moyenne</h5>
                                            <p class="card-text cpt"><?php echo $compteur->get('avgCurYear') ?></p>
                                            <h6 class="card-subtitle mb-2 text-muted">Pour l'année <?php echo date('Y') ?></h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card card-total">
                                        <div class="card-body">
                                            <h5 class="card-title">Total</h5>
                                            <p class="card-text cpt"><?php echo $compteur->get('sumCurYear') ?></p>
                                            <h6 class="card-subtitle mb-2 text-muted">Pour l'année <?php echo date('Y') ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php /* */ ?>
                    <div class="chart-data js-chart-data text-center" data-dates='<?php echo json_encode($compteur->get('chartDates', $days)) ?>' data-label="<?php echo $compteur->get('label') ?>" data-data="<?php echo json_encode($compteur->get('chartData', $days)) ?>">
                        <a class="btn btn-dark" data-bs-toggle="collapse" href="#collapse-<?php echo $k ?>" role="button" aria-expanded="false" aria-controls="collapse-<?php echo $k ?>">
                            Voir le graphique sur <?php echo $days ?> jours
                        </a>
                        <div class="collapse" id="collapse-<?php echo $k ?>">
                            <canvas id="chart-<?php echo $k ?>"></canvas>
                        </div>
                    </div>
                    <?php /* */ ?>
                </section>
            <?php endforeach ?>
        <?php endif ?>
        <?php if(count($meteoData) > 0): ?>
            <section class="meteo" id="meteo-temperature">
                <h2>
                    <a href="#meteo-temperature">
                        Températures et météo
                    </a>
                </h2>
                <p class="legend">
                    <em>Températures relevées à Maugio (données <a href="https://donneespubliques.meteofrance.fr/?fond=produit&id_produit=90&id_rubrique=32" target="_blank">Météo France</a>)</em> ;
                    <em>Indications météo issues de <a href="https://openweathermap.org/" target="_blank">OpenWeatherMap</a> sur Montpellier</em>
                </p>
                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">06h00</th>
                            <th scope="col">12h00</th>
                            <th scope="col">18h00</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($meteoData as $d => $m): ?>
                            <tr>
                                <th scope="row"><?php echo $m['date'] ?></th>
                                <td>
                                    <?php if(isset($weatherData[$d]) && is_array($weatherData[$d]) && isset($weatherData[$d]['06'])): ?>
                                        <i class="wi <?php echo $weatherData[$d]['06']['wi-icon'] ?>"></i>
                                    <?php else: ?>
                                        <i class="wi"></i>
                                    <?php endif ?>
                                    <?php echo $m['06'] ?>
                                </td>
                                <td>
                                    <?php if(isset($weatherData[$d]) && is_array($weatherData[$d]) && isset($weatherData[$d]['12'])): ?>
                                        <i class="wi <?php echo $weatherData[$d]['12']['wi-icon'] ?>"></i>
                                    <?php else: ?>
                                        <i class="wi"></i>
                                    <?php endif ?>
                                    <?php echo $m['12'] ?>
                                </td>
                                <td>
                                    <?php if(isset($weatherData[$d]) && is_array($weatherData[$d]) && isset($weatherData[$d]['18'])): ?>
                                        <i class="wi <?php echo $weatherData[$d]['18']['wi-icon'] ?>"></i>
                                    <?php else: ?>
                                        <i class="wi"></i>
                                    <?php endif ?>
                                    <?php echo $m['18'] ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </section>
        <?php endif ?>
    </div>
    <footer>
        <div class="container">
            <p>
                Cette page n'aurait pas pu exister sans l'équipe de <a href="https://twitter.com/OpenData3M" target="_blank">OpenDataMontpellier</a> que nous remercions vivement.<br>
                Les données sont issues de la ressource <a href="http://data.montpellier3m.fr/dataset/comptages-velo-et-pieton-issus-des-eco-compteurs" target="_blank">Comptages vélo et piéton issus des éco-compteurs</a><br>
                N'oubliez pas de suivre <a href="https://twitter.com/TotemsMtp" target="_blank">@TotemsMtp</a> et <a href="https://twitter.com/TotemAlbert1er" target="_blank">@TotemAlbert1er</a> sur Twitter !<br>
                #JeSuisUnDesDeux - <a href="https://velocite-montpellier.fr" target="_blank">Vélocité Grand Montpellier</a>
            </p>
        </div>
    </footer>
    <?php if(count($compteurs) > 0): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
        <script src="<?php echo Helper::noCache('./assets/js/main.js') ?>"></script>
    <?php endif ?>
</body>
</html>
