<?php
namespace App;
require_once('../bootstrap.php');
$compteurs = (Compteurs::getInstance())->getCompteurs();
$title = 'Les compteurs vélos de Montpellier 3M';
$desc = 'Découvrez les compteurs vélos grâce aux données en Open Data de Montpellier 3M';
$days = 14;
$yesterday = (new \DateTime())->modify('-1 day')->format('d-m-Y');
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
    <meta property="og:url" content="<?php echo _BASE_URL_ ?>" />
    <meta property="og:site_name" content="<?php echo $title ?>" />
    <meta property="og:image" content="<?php echo _BASE_URL_ ?>/assets/img/albert-fb.jpg" />
    <meta property="og:image:secure_url" content="<?php echo _BASE_URL_ ?>/assets/img/albert-fb.jpg" />
    <meta property="og:image:width" content="1000" />
    <meta property="og:image:height" content="500" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="<?php echo _BASE_URL_ ?>" />
    <meta name="twitter:description" content="<?php echo $desc ?>" />
    <meta name="twitter:title" content="<?php echo $title ?>" />
    <meta name="twitter:image" content="<?php echo _BASE_URL_ ?>/assets/img/albert-fb.jpg" />

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous">
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
    <div class="container-lg">
        <h1 class="text-center">Les compteurs vélos de Montpellier 3M</h1>
        <?php if(count($compteurs) > 0): ?>
            <?php foreach($compteurs as $k => $compteur): ?>
                <?php
                    $monthRecord = $compteur->get('monthRecord');
                    $classM = $classY = $classT = $class = '';
                    if($compteur->get('lastValue') == $compteur->get('recordTotal')){
                        $class = 'light';
                        $classM = $classY = $classT = $class;
                    }elseif($compteur->get('lastValue') >= $compteur->get('recordYear')){
                        $class = 'light2';
                        $classM = $classY = $class;
                    }elseif($monthRecord['value'] > 0 && $compteur->get('lastValue') >= $monthRecord['value']){
                        $class = 'light3';
                        $classM = $class;
                    }
                ?>
                <section class="compteur" id="compteur-<?php echo $compteur->get('slug') ?>">
                    <div class="compteur-head">
                        <div class="float-end">
                            <a class="btn btn-outline-info" href="<?php echo _BASE_URL_.'detail/'.$compteur->get('slug') ?>">
                                Voir les stats
                            </a>
                        </div>
                        <h2>
                            <a href="#compteur-<?php echo $compteur->get('slug') ?>">
                                <?php echo $compteur->get('labelHTML') ?>
                            </a>
                        </h2>
                    </div>
                    <div class="row compteur-data">
                        <div class="col-5 col-sm-4 data-col <?php if(file_exists(__DIR__.'/assets/img/'.$k.'.jpg')): ?>with-photo<?php endif ?>">
                            <?php if(file_exists(__DIR__.'/assets/img/'.$k.'.jpg')): ?>
                                <ul class="nav nav-tabs nav-tabs-pm" id="tabs-<?php echo $k ?>" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="link-map-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-map-<?php echo $k ?>" role="tab" aria-controls="tab-map-<?php echo $k ?>" aria-selected="true">Carte</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="link-photo-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-photo-<?php echo $k ?>" role="tab" aria-controls="tab-photo-<?php echo $k ?>" aria-selected="false">Photo</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="tab-content-<?php echo $k ?>">
                                    <div class="tab-pane fade show active" id="tab-map-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-map-<?php echo $k ?>">
                                        <div class="map" id="map-<?php echo $k ?>" data-id="<?php echo $k ?>" data-lat="<?php echo $compteur->get('lat') ?>" data-lng="<?php echo $compteur->get('lng') ?>"></div>
                                    </div>
                                    <div class="tab-pane fade" id="tab-photo-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-photo-<?php echo $k ?>">
                                        <img src="<?php echo _BASE_URL_.'assets/img/'.$k.'.jpg' ?>" />
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="map" id="map-<?php echo $k ?>" data-id="<?php echo $k ?>" data-lat="<?php echo $compteur->get('lat') ?>" data-lng="<?php echo $compteur->get('lng') ?>"></div>
                            <?php endif ?>
                        </div>
                        <div class="col-7 col-sm-8 data-col data-col-2">
                            <div class="row">
                                <div class="col col-12 col-md-6">
                                    <ul class="nav nav-tabs nav-tabs-pm" id="tabs-latest-<?php echo $k ?>" role="tablist">
                                        &nbsp;
                                    </ul>
                                    <div class="tab-content" id="tab-latest-<?php echo $k ?>">
                                        <div class="card card-last <?php echo $class ?>">
                                            <div class="card-body">
                                                <h5 class="card-title">Dernier relevé</h5>
                                                <p class="card-text cpt"><?php echo $compteur->get('lastValue') ?></p>
                                                <h6 class="card-subtitle mb-2 text-muted"><span class="<?php echo ($compteur->get('lastDate') == $yesterday ? '' : 'text-danger') ?>">Le <?php echo $compteur->get('lastDate') ?></span></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col col-12 col-md-6">
                                    <ul class="nav nav-tabs nav-tabs-pm" id="tabs-records-<?php echo $k ?>" role="tablist">
                                        <?php if($monthRecord['value'] > 0): ?>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="link-recordsmonth-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-recordsmonth-<?php echo $k ?>" role="tab" aria-controls="tab-avgmonth-<?php echo $k ?>" aria-selected="false">Mois</a>
                                            </li>
                                        <?php endif ?>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link <?php if($monthRecord['value'] == 0): ?>active<?php endif ?>" id="link-recordsyear-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-recordsyear-<?php echo $k ?>" role="tab" aria-controls="tab-avgyear-<?php echo $k ?>" aria-selected="true">Année</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="link-recordstotal-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-recordstotal-<?php echo $k ?>" role="tab" aria-controls="tab-avgtotal-<?php echo $k ?>" aria-selected="false">Total</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="tab-records-<?php echo $k ?>">
                                        <?php if($monthRecord['value'] > 0): ?>
                                            <div class="tab-pane fade show active" id="tab-recordsmonth-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-recordsmonth-<?php echo $k ?>">
                                                <div class="card card-record <?php echo $classM ?>">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Record du mois</h5>
                                                        <p class="card-text cpt"><?php echo $monthRecord['value'] ?></p>
                                                        <h6 class="card-subtitle mb-2 text-muted">Le <?php echo $monthRecord['date'] ?></h6>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <div class="tab-pane fade <?php if($monthRecord['value'] == 0): ?>show active<?php endif ?>" id="tab-recordsyear-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-recordsyear-<?php echo $k ?>">
                                            <div class="card card-record <?php echo $classY ?>">
                                                <div class="card-body">
                                                    <h5 class="card-title">Record de l'année</h5>
                                                    <p class="card-text cpt"><?php echo $compteur->get('recordYear') ?></p>
                                                    <h6 class="card-subtitle mb-2 text-muted">Le <?php echo $compteur->get('recordYearDate') ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="tab-recordstotal-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-recordstotal-<?php echo $k ?>">
                                            <div class="card card-record <?php echo $classT ?>">
                                                <div class="card-body">
                                                    <h5 class="card-title">Record total</h5>
                                                    <p class="card-text cpt"><?php echo $compteur->get('recordTotal') ?></p>
                                                    <h6 class="card-subtitle mb-2 text-muted">Le <?php echo $compteur->get('recordTotalDate') ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col col-12 col-md-6">
                                    <?php if($monthRecord['value'] > 0): ?>
                                        <ul class="nav nav-tabs nav-tabs-pm" id="tabs-avgmonthyear-<?php echo $k ?>" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="link-avgmonth-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-avgmonth-<?php echo $k ?>" role="tab" aria-controls="tab-avgmonth-<?php echo $k ?>" aria-selected="false">Mois</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="link-avgyear-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-avgyear-<?php echo $k ?>" role="tab" aria-controls="tab-avgyear-<?php echo $k ?>" aria-selected="true">Année</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="link-avgtotal-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-avgtotal-<?php echo $k ?>" role="tab" aria-controls="tab-avgtotal-<?php echo $k ?>" aria-selected="false">Total</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="tab-avgmonthyearcontent-<?php echo $k ?>">
                                        <div class="tab-pane fade show active" id="tab-avgmonth-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-avgmonth-<?php echo $k ?>">
                                            <div class="card card-moy">
                                                <div class="card-body">
                                                    <h5 class="card-title">Moyenne</h5>
                                                    <p class="card-text cpt"><?php echo $monthRecord['avg'] ?></p>
                                                    <h6 class="card-subtitle mb-2 text-muted">Pour le mois <?php echo Helper::frenchMonth(date('m')) ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                            <div class="tab-pane fade" id="tab-avgyear-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-avgyear-<?php echo $k ?>">
                                                <div class="card card-moy">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Moyenne</h5>
                                                        <p class="card-text cpt"><?php echo $compteur->get('avgCurYear') ?></p>
                                                        <h6 class="card-subtitle mb-2 text-muted">Pour l'année <?php echo date('Y') ?></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab-avgtotal-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-avgtotal-<?php echo $k ?>">
                                                <div class="card card-moy">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Moyenne</h5>
                                                        <p class="card-text cpt"><?php echo $compteur->get('avgTotal') ?></p>
                                                        <h6 class="card-subtitle mb-2 text-muted">Depuis le <?php echo $compteur->get('firstDate') ?></h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="card card-moy">
                                            <div class="card-body">
                                                <h5 class="card-title">Moyenne</h5>
                                                <p class="card-text cpt"><?php echo $compteur->get('avgCurYear') ?></p>
                                                <h6 class="card-subtitle mb-2 text-muted">Pour l'année <?php echo date('Y') ?></h6>
                                            </div>
                                        </div>
                                    <?php endif ?>
                                </div>
                                <div class="col col-12 col-md-6">
                                    <?php if($monthRecord['value'] > 0): ?>
                                        <ul class="nav nav-tabs nav-tabs-pm" id="tabs-summonthyear-<?php echo $k ?>" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="link-summonth-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-summonth-<?php echo $k ?>" role="tab" aria-controls="tab-summonth-<?php echo $k ?>" aria-selected="false">Mois</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="link-sumyear-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-sumyear-<?php echo $k ?>" role="tab" aria-controls="tab-sumyear-<?php echo $k ?>" aria-selected="true">Année</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="link-sumtotal-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-sumtotal-<?php echo $k ?>" role="tab" aria-controls="tab-sumtotal-<?php echo $k ?>" aria-selected="true">Total</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content" id="tab-content-summonthyear-<?php echo $k ?>">
                                            <div class="tab-pane fade show active" id="tab-summonth-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-summonth-<?php echo $k ?>">
                                                <div class="card card-total">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Total</h5>
                                                        <p class="card-text cpt"><?php echo $monthRecord['sum'] ?></p>
                                                        <h6 class="card-subtitle mb-2 text-muted">Pour le mois <?php echo Helper::frenchMonth(date('m')) ?></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab-sumyear-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-sumyear-<?php echo $k ?>">
                                                <div class="card card-total">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Total</h5>
                                                        <p class="card-text cpt"><?php echo $compteur->get('sumCurYear') ?></p>
                                                        <h6 class="card-subtitle mb-2 text-muted">Pour l'année <?php echo date('Y') ?></h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab-sumtotal-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-sumtotal-<?php echo $k ?>">
                                                <div class="card card-total">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Total</h5>
                                                        <p class="card-text cpt"><?php echo $compteur->get('sumTotal') ?></p>
                                                        <h6 class="card-subtitle mb-2 text-muted">Depuis le <?php echo $compteur->get('firstDate') ?></h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="card card-total">
                                            <div class="card-body">
                                                <h5 class="card-title">Total</h5>
                                                <p class="card-text cpt"><?php echo $compteur->get('sumCurYear') ?></p>
                                                <h6 class="card-subtitle mb-2 text-muted">Pour l'année <?php echo date('Y') ?></h6>
                                            </div>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php /*  ?>
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
            <?php require_once('./parts/note-footer.php') ?>
        </div>
    </footer>
    <?php if(count($compteurs) > 0): ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
        <script src="<?php echo Helper::noCache('./assets/js/main.js') ?>"></script>
    <?php endif ?>
    <?php require_once('./parts/matomo.php') ?>
</body>
</html>
