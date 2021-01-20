<?php
namespace App;
require_once('../bootstrap.php');
$compteurs = (Compteurs::getInstance())->getCompteurs();
$title = 'La galaxie des compteurs vélos de Montpellier 3M';
$desc = 'La galaxie des compteurs vélos, grâce aux données en Open Data de Montpellier 3M';
$rowData = (Compteurs::getInstance())->getAllByDates();
$cptLabels = (Compteurs::getInstance())->getLabels();
$yesterday = (new \DateTime())->modify('-1 day')->format('d-m-Y');
$maxDays = date('z')+1;
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
    <link href="https://fonts.googleapis.com/css2?family=Nanum+Pen+Script&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <link type="text/css" rel="stylesheet" href="<?php echo _BASE_URL_.Helper::noCache('assets/css/galaxie.css') ?>" media="all" />
    <link type="text/css" rel="stylesheet" href="<?php echo _BASE_URL_.Helper::noCache('assets/css/weather-icons.min.css') ?>" media="all" />
	<link rel="apple-touch-icon" sizes="90x90" href="<?php echo _BASE_URL_ ?>assets/img/favicons/favicon.png">
	<link rel="icon" type="image/x-icon" sizes="90x90" href="<?php echo _BASE_URL_ ?>assets/img/favicons/favicon.png">
</head>
<body>
    <div class="row">
        <div class="col-12 col-sm-9 col-md-7 main" id="scroll">
            <section data-place="overview" class="overview">
                <h1 class="page-header">Montpellier</h1>
                <div class="content">
                    <p>
                        La légende raconte qu'il n'y <a href="javascript:void(0);" data-video="h_FPzj10dcg" class="modal-yt">aurait que 2 cyclistes à Montpellier</a>.
                    </p>
                    <p>
                        Enfin c'est une légende, parce que d'autres disent que <a href="javascript:void(0);" data-video="cg85WmNpTkU" class="modal-yt">les cyclistes sont plutôt présents</a>, il parait même qu'ils en ont <a href="javascript:void(0)" data-video="m42RS2Xu7sY" class="modal-yt">fait un chanson</a>.
                    </p>
                    <p>
                        Toujours est-il que depuis, la Métropole 3M a décidé d'installer des compteurs, et force est de constater qu'is tournent beaucoup ces 2 cyclistes :
                    </p>
                    <canvas id="linechart-general" class="linechart"
                        data-label="par semaine"
                        data-labels='<?php echo json_encode(array_values($rowData['dates'])) ?>'
                        data-values='<?php echo json_encode($rowData['data']) ?>'
                        data-cpts='<?php echo json_encode($cptLabels) ?>'
                    >
                    </canvas>
                    <p>
                        Et si on allait regarder d'un peu plus près tout ça ? Suivez-le guide !
                    </p>
                </div>
            </section>
            <?php if(count($compteurs) > 0): ?>
                <?php foreach($compteurs as $k => $compteur): ?>
                    <section data-place="<?php echo $compteur->get('slug') ?>" class="compteur" data-lat="<?php echo $compteur->get('lat') ?>" data-lng="<?php echo $compteur->get('lng') ?>" data-zoom="17">
                        <h2><?php echo $compteur->get('labelHTML') ?></h2>
                        <?php if($compteur->get('slug') == 'albert-1er'): ?>
                            <p>
                                Le premier à avoir fait son apparition est le <a href="javascript:void(0);" data-img="<?php echo _BASE_URL_ ?>/assets/img/albert-fb.jpg" class="modal-img">Totem Albert 1<sup>er</sup></a>, installé le 12 mars 2020.<br>
                                Depuis décembre, et l'ouverture en données publiques, c'est le compteur de tous les records. Pour preuve voici les chiffres des records en fonction des jours de la semaine :
                            </p>
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
                        <?php elseif($compteur->get('slug') == 'berracasa'): ?>
                            <p>
                                L'allée Alegria Berracasa, vous connaissez ? Peut-être pas son nom, mais vous êtes forcément passé par là si vous avez découvert Montpellier. Les bords du Lez, le FISE, l'ancien hôtel de région dessiné par Ricardo Bofill, la vue imprenable sur l'Arbre Blanc, ...
                            </p>
                            <p>
                                C'est le compteur mixte, rassemblant autant ceux qui se déplacent pour le travail que pour le loisir dominical.
                            </p>
                            <?php $weeks = $compteur->get('weeks') ?>
                            <?php if(count($weeks) > 0): ?>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-title">
                                            <h6>Chiffres par semaine (en <?php echo date('Y') ?>)</h6>
                                        </div>
                                        <canvas id="bar-week-<?php echo $k ?>" class="bar bar-weeks" data-label="par semaine" data-labels='<?php echo json_encode(array_keys($weeks)) ?>' data-values='<?php echo json_encode(array_column($weeks, 'avg')) ?>' <?php /* data-global-avg="<?php echo $compteur->get('avgCurYear') ?>" /* */ ?>></canvas>
                                    </div>
                                </div>
                            <?php endif ?>
                        <?php elseif($compteur->get('slug') == 'celleneuve'): ?>
                            <p>
                                Celleneuve c'est le compteur le plus haut nord disponible à Montpellier.
                            </p>
                            <div class="container">
                                <div class="row">
                                    <div class="col col-12 col-md-6">
                                        <div class="card card-data <?php echo ($compteur->get('lastValue') == $compteur->get('recordTotal')?'light':'') ?>">
                                            <div class="card-body">
                                                <h5 class="card-title">Dernier relevé</h5>
                                                <p class="card-text cpt"><?php echo $compteur->get('lastValue') ?></p>
                                                <h6 class="card-subtitle mb-2 text-muted"><span class="<?php echo ($compteur->get('lastDate') == $yesterday ? '' : 'text-danger') ?>">Le <?php echo $compteur->get('lastDate') ?></span></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col col-12 col-md-6">
                                        <div class="card card-data <?php echo ($compteur->get('lastValue') == $compteur->get('recordTotal')?'light':'') ?>">
                                            <div class="card-body">
                                                <h5 class="card-title">Record</h5>
                                                <p class="card-text cpt"><?php echo $compteur->get('recordTotal') ?></p>
                                                <h6 class="card-subtitle mb-2 text-muted">Le <?php echo $compteur->get('recordTotalDate') ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php elseif($compteur->get('slug') == 'delmas-1'): ?>
                            <p>
                                L'avenue François Delmas, c'est l'ancienne route de Nîmes, celle qui dessert les communes de l'est de Montpellier.
                            </p>
                            <p>
                                Attention, à cet endroit la piste est mono-directionnelle (est-ouest).
                            </p>
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
                        <?php elseif($compteur->get('slug') == 'delmas-2'): ?>
                            <p>
                                Son voisin, Delmas 2, dans le sens ouest-est, est le plus mal loti. En effet, depuis avril 2020 une piste transitoire a élargie la mini bande cyclable où il est installé, permettant de franchir le Lez de manière plus sécurisée.<br>
                                Du coup, sur tous les cyclistes qui se dirigent vers Castelnau-le-Lez, peu posent leurs roues sur le bas-côté pour être comptés.
                            </p>
                            <img src="<?php echo _BASE_URL_ ?>/assets/img/X2H20063164.jpg">
                        <?php elseif($compteur->get('slug') == 'gerhardt'): ?>
                            <p>
                                Non loin de la maison natale de Juliette Greco, la couleur orangée de la piste de la rue Gerhardt vous donnera peut-être envie de chantonner Jolie Môme ou La Javanaise tout en pédalant.
                            </p>
                            <?php $monthes = $compteur->get('monthes') ?>
                            <?php if(count($monthes) > 0): ?>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-title">
                                            <h6>Chiffres par mois (en <?php echo date('Y') ?>)</h6>
                                            <div>
                                                <canvas id="bar-month-<?php echo $k ?>" class="bar bar-monthes" data-label="par mois " data-labels='<?php echo json_encode(array_map(Helper::class.'::frenchMonthWithoutPrefix', array_keys($monthes))) ?>' data-values='<?php echo json_encode(array_column($monthes, 'avg')) ?>' data-global-avg="<?php echo $compteur->get('avgCurYear') ?>"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif ?>
                        <?php elseif($compteur->get('slug') == 'lattes-1'): ?>
                            <p>
                                Un petit tour à la plage ça vous dit ? Malgré son nom, le compteur Lattes 1 (comme son voisin Lattes 2) sont situés sur la commune de Pérols, l'une des pistes pour rejoindre les plages...
                            </p>
                            <div class="container">
                                <div class="row">
                                    <div class="col col-12 col-md-6">
                                        <div class="card card-data">
                                            <div class="card-body">
                                                <h5 class="card-title">Total en <?php echo date('Y') ?></h5>
                                                <p class="card-text cpt"><?php echo $compteur->get('sumCurYear') ?></p>
                                                <h6 class="card-subtitle mb-2 text-muted">Au <?php echo $compteur->get('recordTotalDate') ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col col-12 col-md-6">
                                        <div class="card card-data <?php echo ($compteur->get('lastValue') == $compteur->get('recordTotal')?'light':'') ?>">
                                            <div class="card-body">
                                                <h5 class="card-title">Moyenne </h5>
                                                <p class="card-text cpt"><?php echo $compteur->get('avgCurYear') ?></p>
                                                <h6 class="card-subtitle mb-2 text-muted">Moyenne quotidienne en <?php echo date('Y') ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php elseif($compteur->get('slug') == 'lattes-2'): ?>
                            <p>
                                À l'image de Delmas 2, le compteur "retour des plages" est moins utilisé, les usagers du vélo lui préférant la piste de Lattes 1 plus bucolique.<br>
                                Mais il sera prochainement aux premières loges pour assister aux matchs du MHSC...
                            </p>
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h6>Passages par jour</h6>
                                        <em>En <?php echo date('Y') ?></em>
                                    </div>
                                    <canvas id="bar-day-<?php echo $compteur->get('id') ?>" class="bar-detail bar-day" data-labels='<?php echo json_encode($compteur->get('chartDates', $maxDays)) ?>' data-values='<?php echo json_encode($compteur->get('chartData', $maxDays)) ?>'></canvas>
                                </div>
                            </div>
                        <?php elseif($compteur->get('slug') == 'laverune'): ?>
                            <p>
                                À Lavérune, on retrouve le compteur le plus occidental de Montpellier. Il semble très prisés des habitants pour les balades dominicales.
                            </p>
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">
                                        <h6>Semaine vs week-end</h6>
                                        <em>En <?php echo date('Y') ?></em>
                                    </div>
                                    <canvas id="pie-day2-<?php echo $k ?>" class="pie pie-days2" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values($compteur->getWeekWeekend(date('Y')))) ?>'></canvas>
                                </div>
                            </div>
                        <?php elseif($compteur->get('slug') == 'vieille-poste'): ?>
                            <p>
                                Le compteur de la rue de la Vieille-Poste, c'est celui d'une partie du quartier du Millénaire, où de nombreuses entreprises sont installées.
                            </p>
                            <div class="container">
                                <div class="row">
                                    <div class="col col-12 col-md-6">
                                        <div class="card card-data <?php echo ($compteur->get('lastValue') == $compteur->get('recordTotal')?'light':'') ?>">
                                            <div class="card-body">
                                                <h5 class="card-title">Dernier relevé</h5>
                                                <p class="card-text cpt"><?php echo $compteur->get('lastValue') ?></p>
                                                <h6 class="card-subtitle mb-2 text-muted"><span class="<?php echo ($compteur->get('lastDate') == $yesterday ? '' : 'text-danger') ?>">Le <?php echo $compteur->get('lastDate') ?></span></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col col-12 col-md-6">
                                        <div class="card card-data <?php echo ($compteur->get('lastValue') == $compteur->get('recordTotal')?'light':'') ?>">
                                            <div class="card-body">
                                                <h5 class="card-title">Record</h5>
                                                <p class="card-text cpt"><?php echo $compteur->get('recordTotal') ?></p>
                                                <h6 class="card-subtitle mb-2 text-muted">Le <?php echo $compteur->get('recordTotalDate') ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                        <p class="text-center p-btn">
                            <a class="btn btn-secondary btn-lg" href="<?php echo _BASE_URL_.'detail/'.$compteur->get('slug') ?>">
                                Voir les stats de <?php echo $compteur->get('labelHTML') ?>
                            </a>
                        </p>
                    </section>
                <?php endforeach ?>
            <?php endif ?>
        </div>
        <div id="map" class="d-none d-sm-block col-sm-3 col-md-5 sidebar"></div>
    </div>
    <div class="modal" tabindex="-1" id="theModal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    xxx
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.6.0/underscore-min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@0.5.7/chartjs-plugin-annotation.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script src="<?php echo _BASE_URL_.Helper::noCache('./assets/js/main.js') ?>"></script>
    <script src="<?php echo _BASE_URL_.Helper::noCache('./assets/js/galaxie.js') ?>"></script>
</body>
</html>
