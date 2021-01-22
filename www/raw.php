<?php
namespace App;
require_once('../bootstrap.php');
$compteurs = (Compteurs::getInstance())->getCompteurs();
$totems = [];
$yesterday = (new \DateTime())->modify('-1 day')->format('d-m-Y');
$rowData = (Compteurs::getInstance())->getAllByDates();
$cptLabels = (Compteurs::getInstance())->getLabels();
$tomtom = (Tomtom::getInstance())->getData();
?><!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compteurs : données</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <style>
        h2{
            margin-top: 1.5rem;
        }
        h3{
            margin-top: 2rem;
        }
        h2 a, h2 a:hover, h2 a:visited, h2 a:active{
            color: #000;
            text-decoration: none;
        }
        div.recap{
            background: #eef;
        }
        #dl{
            margin: 20px 0;
        }
        #dl a.badge{
            text-decoration: none;
        }
        #dl a.badge:hover{
            color: #fff;
            background: #ffc107 !important;
        }
        .raw-data-group{
            margin: 50px 0;
        }
        .raw-data-group em{
            font-size: 0.7em;
        }
        .raw-data{
            max-height: 400px;
            overflow-y: scroll;
        }
        .row-cards{
            margin-bottom: 50px;
        }
        .bd-example{
            padding: 0.4rem 1rem;
            border-radius: .25rem;
            border: thin solid #dee2e6;
        }
        #menu-tab{
            background: white;
        }
        .linechart{
            margin: 50px 0;
        }
        .bg-mid-success{
            background: #d1e7dd;
        }
        .bg-success{
            background: #479f76;
        }
    </style>
</head>
<body>
    <section id="dl" class="row">
        <div class="col-12 text-center">
            <a href="./raw-dl.php" download class="btn btn-primary">Télécharger les données (CSV)</a>
            <a href="./meteo-dl.php" download class="btn btn-dark">Télécharger les températures (CSV)</a>
            <a href="#infometeo" class="badge rounded-pill bg-secondary"  data-bs-toggle="modal" data-bs-target="#modal-doc-meteo">
                <span class="fa fa-question">?</span>
            </a>
        </div>
    </section>
    <?php if(count($compteurs) > 0): ?>
        <section id="menu-tab" class="sticky-top">
            <?php $first = true ?>
            <div class="bd-example">
                <ul class="nav nav-pills" role="tablist">
                    <?php foreach($compteurs as $k => $compteur): ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link <?php echo ($first ? 'active' :'') ?>" id="link-tab-<?php echo $k ?>" data-bs-toggle="tab" href="#tab-<?php echo $k ?>" role="tab" aria-controls="tab-<?php echo $k ?>" aria-selected="true">
                                <?php echo $compteur->get('labelHTML') ?>
                            </a>
                        </li>
                        <?php $first = false ?>
                    <?php endforeach ?>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link bg-info" id="link-tab-general" data-bs-toggle="tab" href="#tab-general" role="tab" aria-controls="tab-general" aria-selected="true">
                            Général
                        </a>
                    </li>
                </ul>
            </div>
        </section>
    <?php endif ?>
    <section id="main" class="container">
        <div class="row">
            <?php if(count($compteurs) > 0): ?>
                <div class="tab-content">
                    <?php $first = true ?>
                    <?php foreach($compteurs as $k => $compteur): ?>
                        <div class="tab-pane fade <?php echo ($first ? 'show active' :'') ?>" id="tab-<?php echo $k ?>" role="tabpanel" aria-labelledby="tab-<?php echo $k ?>">
                            <?php
                                $data = $compteur->get('dataTotal');
                                $totems[$k] = $compteur->get('label');
                                $monthes = $compteur->get('monthes');
                                $currentMonth = (is_array($monthes) && isset($monthes[date('m')]) ? $monthes[date('m')] : null);
                                $latestColor = ($compteur->get('lastDate') == $yesterday ? 'success' : 'danger');
                            ?>
                            <div class="row">
                                <section class="compteur" id="compteur-<?php echo $compteur->get('slug') ?>">
                                    <h2 class="text-center">
                                        <a href="#compteur-<?php echo $compteur->get('slug') ?>">
                                            <?php echo $compteur->get('labelHTML') ?>
                                        </a>
                                    </h2>
                                    <p class="text-center">ID : <?php echo $compteur->get('id') ?> | <span class="text-<?php echo $latestColor ?>">Dernier relevé : <b><?php echo $compteur->get('lastValue') ?></b> le <?php echo $compteur->get('lastDate') ?></span></p>
                                    <div class="row row-cards">
                                        <div class="col">
                                            <div class="card bg-info">
                                                <div class="card-body">
                                                    <div class="card-title"><b>Cette année <?php echo date('Y') ?></b></div>
                                                    <ul class="card-text">
                                                        <li>
                                                            Total : <?php echo $compteur->get('sumCurYear') ?>
                                                        </li>
                                                        <li>
                                                            Moyenne : <?php echo $compteur->get('avgCurYear') ?>
                                                        </li>
                                                        <li>
                                                            Record : <?php echo $compteur->get('recordYear') ?> le <?php echo $compteur->get('recordYearDate') ?>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(!is_null($currentMonth)): ?>
                                            <div class="col">
                                                <div class="card bg-light">
                                                    <div class="card-body">
                                                        <div class="card-title"><b>Ce mois-ci <?php echo Helper::frenchMonth(date('m')) ?> <?php echo date('Y') ?></b></div>
                                                        <ul class="card-text">
                                                            <li>
                                                                Total : <?php echo $currentMonth['sum'] ?>
                                                            </li>
                                                            <li>
                                                                Moyenne : <?php echo $currentMonth['avg'] ?>
                                                            </li>
                                                            <li>
                                                                Record : <?php echo $currentMonth['value'] ?> le <?php echo $currentMonth['date'] ?>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                        <div class="col">
                                            <div class="card bg-warning">
                                                <div class="card-body">
                                                    <div class="card-title"><b>Toutes les données</b></div>
                                                    <ul class="card-text">
                                                        <li>
                                                            Total : <?php echo $compteur->get('sumTotal') ?>
                                                        </li>
                                                        <li>
                                                            Moyenne : <?php echo $compteur->get('avgTotal') ?>
                                                        </li>
                                                        <li>
                                                            Record : <?php echo $compteur->get('recordTotal') ?> le <?php echo $compteur->get('recordTotalDate') ?>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h3>Statistiques par jours</h3>
                                    <div class="row">
                                        <div class="col">
                                            <?php $days = $compteur->get('days') ?>
                                            <?php if(count($days) > 0): ?>
                                                <b>Par jour de la semaine (toutes les données)</b>
                                                <table class="text-center table table-striped table-hover align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Jour</th>
                                                            <th>Total</th>
                                                            <th>Moyenne</th>
                                                            <th>Record</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($days as $dow => $values): ?>
                                                            <tr>
                                                                <th>
                                                                    <?php echo Helper::frenchDayOfTheWeek($dow) ?>
                                                                </th>
                                                                <td>
                                                                    <?php echo $values['sum'] ?><br>
                                                                    (<?php echo $values['cpt'] ?> jours)
                                                                </td>
                                                                <td>
                                                                    <?php echo $values['avg'] ?><br>
                                                                    &nbsp;
                                                                </td>
                                                                <td>
                                                                    <?php echo $values['value'] ?><br>
                                                                    (<?php echo $values['date'] ?>)
                                                                </td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                </table>
                                                <div>
                                                    <canvas id="bar-day-<?php echo $k ?>" class="bar bar-days" data-label="par jour " data-labels='<?php echo json_encode(array_map(Helper::class.'::frenchDayOfTheWeek', array_keys($days))) ?>' data-values='<?php echo json_encode(array_column($days, 'avg')) ?>' data-global-avg="<?php echo $compteur->get('avgTotal') ?>" data-max="<?php echo $compteur->get('recordTotal') ?>"></canvas>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                        <div class="col">
                                            <?php $days = $compteur->get('days-by-year') ?>
                                            <?php if(count($days) > 0 && isset($days[date('Y')]) && count($days[date('Y')]) > 0): ?>
                                                <?php $days = $days[date('Y')] ?>
                                                <b>Par jour de la semaine (en <?php echo date('Y') ?>)</b>
                                                <table class="text-center table table-striped table-hover align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Jour</th>
                                                            <th>Total</th>
                                                            <th>Moyenne</th>
                                                            <th>Record</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($days as $dow => $values): ?>
                                                            <tr>
                                                                <th>
                                                                    <?php echo Helper::frenchDayOfTheWeek($dow) ?>
                                                                </th>
                                                                <td>
                                                                    <?php echo $values['sum'] ?><br>
                                                                    (<?php echo $values['cpt'] ?> jours)
                                                                </td>
                                                                <td>
                                                                    <?php echo $values['avg'] ?><br>&nbsp;
                                                                </td>
                                                                <td>
                                                                    <?php echo $values['value'] ?><br>
                                                                    (<?php echo $values['date'] ?>)
                                                                </td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                </table>
                                                <div>
                                                    <canvas id="bar-day2-<?php echo $k ?>" class="bar bar-days2" data-label="par jour" data-labels='<?php echo json_encode(array_map(Helper::class.'::frenchDayOfTheWeek', array_keys($days))) ?>' data-values='<?php echo json_encode(array_column($days, 'avg')) ?>' data-global-avg="<?php echo $compteur->get('avgCurYear') ?>" data-max="<?php echo $compteur->get('recordTotal') ?>"></canvas>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                    <h3>Semaine vs week-end</h3>
                                    <div class="row">
                                        <div class="col">
                                            <b>Toutes les données</b>
                                            <div>
                                                <canvas id="pie-day-<?php echo $k ?>" class="pie pie-days" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values($compteur->getWeekWeekend())) ?>'></canvas>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <b>En <?php echo date('Y') ?></b>
                                            <div>
                                                <canvas id="pie-day2-<?php echo $k ?>" class="pie pie-days2" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values($compteur->getWeekWeekend(date('Y')))) ?>'></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <h3>Autres statistiques</h3>
                                    <div class="row">
                                        <div class="col">
                                            <?php if(count($monthes) > 0): ?>
                                                <b>Chiffres par mois (en <?php echo date('Y') ?>)</b>
                                                <table class="table table-striped table-hover text-center align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Mois</th>
                                                            <th>Total</th>
                                                            <th>Moyenne</th>
                                                            <th>Record</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($monthes as $month => $values): ?>
                                                            <tr>
                                                                <th>
                                                                    <?php echo Helper::frenchMonth($month, false) ?>
                                                                </th>
                                                                <td>
                                                                    <?php echo $values['sum'] ?><br>
                                                                    (<?php echo $values['cpt'] ?> jours)
                                                                </td>
                                                                <td>
                                                                    <?php echo $values['avg'] ?><br>
                                                                    &nbsp;
                                                                </td>
                                                                <td>
                                                                    <?php echo $values['value'] ?><br>
                                                                    (<?php echo $values['date'] ?>)
                                                                </td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                    </tbody>
                                                </table>
                                                <div>
                                                    <canvas id="bar-month-<?php echo $k ?>" class="bar bar-monthes" data-label="par mois " data-labels='<?php echo json_encode(array_map(Helper::class.'::frenchMonthWithoutPrefix', array_keys($monthes))) ?>' data-values='<?php echo json_encode(array_column($monthes, 'avg')) ?>' data-global-avg="<?php echo $compteur->get('avgCurYear') ?>"></canvas>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                        <div class="col">
                                            <?php $weeks = $compteur->get('weeks') ?>
                                            <?php if(count($weeks) > 0): ?>
                                                <b>Chiffres par semaine (en <?php echo date('Y') ?>)</b>
                                                <table class="text-center table table-striped table-hover align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>N° semaine</th>
                                                            <th>Total</th>
                                                            <th>Moyenne</th>
                                                            <th>Record</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($weeks as $week => $values): ?>
                                                            <tr>
                                                                <th>
                                                                    <?php echo $week ?>
                                                                </th>
                                                                <td>
                                                                    <?php echo $values['sum'] ?><br>
                                                                    (<?php echo $values['cpt'] ?> jours)
                                                                </td>
                                                                <td>
                                                                    <?php echo $values['avg'] ?><br>
                                                                    &nbsp;
                                                                </td>
                                                                <td>
                                                                    <?php echo $values['value'] ?><br>
                                                                    (<?php echo $values['date'] ?>)
                                                                </td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                </table>
                                                <div>
                                                    <canvas id="bar-week-<?php echo $k ?>" class="bar bar-weeks" data-label="par semaine" data-labels='<?php echo json_encode(array_keys($weeks)) ?>' data-values='<?php echo json_encode(array_column($weeks, 'avg')) ?>' <?php /* data-global-avg="<?php echo $compteur->get('avgCurYear') ?>" /* */ ?>></canvas>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                    <?php if(is_array($data) && count($data) > 0): ?>
                                        <div class="row raw-data-group">
                                            <div class="col-12 col-sm-6">
                                                <div class="">
                                                    <strong>Données brutes</strong>
                                                    <em>(Scroll au sein du tableau)</em>
                                                    <?php $max = 0 ?>
                                                    <ul class="list-group raw-data">
                                                        <?php foreach($data as $date => $val): ?>
                                                            <?php if($val > $max){ $class = 'bg-mid-success'; $max = $val; }else{ $class = '';} ?>
                                                            <li class="list-group-item <?php echo $class ?> <?php echo ($compteur->get('recordTotal') == $val ? 'bg-success bg-gradient' : '') ?>">
                                                                <?php echo $date ?> : <b><?php echo $val ?></b>
                                                            </li>
                                                        <?php endforeach ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <?php $stack = $compteur->getSumStack() ?>
                                            <div class="col-12 col-sm-6">
                                                <b>Valeurs incrémentales</b>
                                                <div>
                                                    <canvas id="stack-<?php echo $k ?>" class="bar-stack" data-labels='<?php echo json_encode(array_keys($stack)) ?>' data-values='<?php echo json_encode(array_values($stack)) ?>' data-max="<?php echo max($stack) ?>"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif ?>
                                </section>
                            </div>
                        </div>
                        <?php $first = false ?>
                    <?php endforeach ?>
                    <div class="tab-pane fade" id="tab-general" role="tabpanel" aria-labelledby="tab-general">
                        <div class="row">
                            <canvas id="linechart-general" class="linechart"
                                data-label="par semaine"
                                data-labels='<?php echo json_encode(array_values($rowData['dates'])) ?>'
                                data-values='<?php echo json_encode($rowData['data']) ?>'
                                data-cpts='<?php echo json_encode($cptLabels) ?>'
                            >
                            </canvas>
                        </div>
                        <h3>Semaine vs week-end</h3>
                        <div class="row">
                            <div class="col">
                                <b>Toutes les données</b>
                                <div>
                                    <canvas id="pie-day-general" class="pie pie-days" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values((Compteurs::getInstance())->getWeekWeekend())) ?>'></canvas>
                                </div>
                            </div>
                            <div class="col">
                                <b>En <?php echo date('Y') ?></b>
                                <div>
                                    <canvas id="pie-day2-general" class="pie pie-days2" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values((Compteurs::getInstance())->getWeekWeekend(date('Y')))) ?>'></canvas>
                                </div>
                            </div>
                        </div>
                        <?php if(is_array($tomtom) && count($tomtom) > 0): ?>
                            <h3>Congestion automobile (données Tomtom)</h3>
                            <em>%age de <a href="https://www.tomtom.com/blog/road-traffic/urban-traffic-congestion/" target="_blank">congestion automobile</a> à l'échelle de la ville</em>
                            <div class="row" id="tomtom">
                                <canvas id="tomtom-day" class="bar-tomtom" data-labels='<?php echo json_encode(array_column($tomtom, 'date')) ?>' data-values='<?php echo json_encode(array_column($tomtom, 'congestion')) ?>'></canvas>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </section>
    <div class="modal" tabindex="-1" id="modal-doc-meteo">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Documentation météo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-left">
                    <h6>Doc Météo</h6>
                    <p>Les données météos sont issue du format SYNOP de Météo France : <a href="ftp://esurfmar.meteo.fr/pub/pb/tmp/doc_util_bdmo-v13.pdf" target="_blank">documentation</a> et <a href="https://donneespubliques.meteofrance.fr/client/document/doc_parametres_synop_168.pdf" target="_blank">autre documentation</a> : </p>
                    <table class="table">
                        <tbody>
                            <tr><th class="col-2">date</th><td class="col-10">date du relevé</td></tr>
                            <tr><th class="col-2">temp. 06h00</th><td class="col-10">température à 06h, avec °C derrière</td></tr>
                            <tr><th class="col-2">temp. 12h00</th><td class="col-10">température à 12h, avec °C derrière</td></tr>
                            <tr><th class="col-2">temp. 18h00</th><td class="col-10">température à 18h, avec °C derrière</td></tr>
                            <tr><th class="col-2">temp. 06h00 (brut)</th><td class="col-10">température à 06h, sans °C derrière</td></tr>
                            <tr><th class="col-2">temp. 12h00 (brut)</th><td class="col-10">température à 12h, sans °C derrière</td></tr>
                            <tr><th class="col-2">temp. 18h00 (brut)</th><td class="col-10">température à 18h, sans °C derrière</td></tr>
                            <tr><th class="col-2">rr3 (06h)</th><td class="col-10">valeur rr3 (précipitations dans les 3 heures pécédentes) à 6h</td></tr>
                            <tr><th class="col-2">rr6 (06h)</th><td class="col-10">valeur rr3 (précipitations dans les 6 heures pécédentes) à 6h</td></tr>
                            <tr><th class="col-2">rr12 (06h)</th><td class="col-10">valeur rr3 (précipitations dans les 12 heures pécédentes) à 6h</td></tr>
                            <tr><th class="col-2">rr24 (06h)</th><td class="col-10">valeur rr3 (précipitations dans les 24 heures pécédentes) à 6h</td></tr>
                            <tr><th class="col-2">ff (06h)</th><td class="col-10">valeur ff (force du vent) à 6h</td></tr>
                            <tr><th class="col-2">dd (06h)</th><td class="col-10">valeur dd (direction du vent) à 6h</td></tr>
                            <tr><th class="col-2">cod_tend (06h)</th><td class="col-10">valeur cod_tend (type tendance baro) à 6h</td></tr>
                            <tr><th class="col-2">rr3 (12h)</th><td class="col-10">valeur rr3 (précipitations dans les 3 heures pécédentes) à 12h</td></tr>
                            <tr><th class="col-2">rr6 (12h)</th><td class="col-10">valeur rr3 (précipitations dans les 6 heures pécédentes) à 12h</td></tr>
                            <tr><th class="col-2">rr12 (12h)</th><td class="col-10">valeur rr3 (précipitations dans les 12 heures pécédentes) à 12h</td></tr>
                            <tr><th class="col-2">rr24 (12h)</th><td class="col-10">valeur rr3 (précipitations dans les 24 heures pécédentes) à 12h</td></tr>
                            <tr><th class="col-2">ff (12h)</th><td class="col-10">valeur ff (force du vent) à 12h</td></tr>
                            <tr><th class="col-2">dd (12h)</th><td class="col-10">valeur dd (direction du vent) à 12h</td></tr>
                            <tr><th class="col-2">cod_tend (12h)</th><td class="col-10">valeur cod_tend (type tendance baro) à 12h</td></tr>
                            <tr><th class="col-2">rr3 (18h)</th><td class="col-10">valeur rr3 (précipitations dans les 3 heures pécédentes) à 18h</td></tr>
                            <tr><th class="col-2">rr6 (18h)</th><td class="col-10">valeur rr3 (précipitations dans les 6 heures pécédentes) à 18h</td></tr>
                            <tr><th class="col-2">rr12 (18h)</th><td class="col-10">valeur rr3 (précipitations dans les 12 heures pécédentes) à 18h</td></tr>
                            <tr><th class="col-2">rr24 (18h)</th><td class="col-10">valeur rr3 (précipitations dans les 24 heures pécédentes) à 18h</td></tr>
                            <tr><th class="col-2">ff (18h)</th><td class="col-10">valeur ff (force du vent) à 18h</td></tr>
                            <tr><th class="col-2">dd (18h)</th><td class="col-10">valeur dd (direction du vent) à 18h</td></tr>
                            <tr><th class="col-2">cod_tend (18h)</th><td class="col-10">valeur cod_tend (type tendance baro) à 18h</td></tr>
                        </tbody>
                    </table>
                    <p>Puis sont ajoutées les données de OpenWeatherMap, il s'agit d'un code numérique : <a href="https://openweathermap.org/weather-conditions#Weather-Condition-Codes-2" target="_blank">voir ici</a></p>
                    <table class="table">
                        <tbody>
                            <tr><th class="col-2">weather_06</th><td class="col-10">code OpenWeatherMap à 6h</td></tr>
                            <tr><th class="col-2">weather_12</th><td class="col-10">code OpenWeatherMap à 12h</td></tr>
                            <tr><th class="col-2">weather_12</th><td class="col-10">code OpenWeatherMap à 18h</td></tr>
                        </tbody>
                    </table>
                    <p>Nota : l'absence de donnée est remplaceé par "-"</p>
                </div>
            </div>
        </div>
    </div>
    <div id="chartjs-tooltip"><table></table></div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@0.5.7/chartjs-plugin-annotation.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script type="text/javascript">
        function shuffle(a) {
            var j, x, i;
            for (i = a.length - 1; i > 0; i--) {
                j = Math.floor(Math.random() * (i + 1));
                x = a[i];
                a[i] = a[j];
                a[j] = x;
            }
            return a;
        }
        $('document').ready(function(){
            //var colors = ['#3d0a91', '#ab296a', '#146c43', '#0dcaf0', '#ffc107'];
            var colors = ['#75cbb7', '#cae26e'];
            $('.pie').each(function(){
                var self = $(this);
                var ctx = document.getElementById(self.attr('id')).getContext('2d');
                var myPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: self.data('labels'),
                        datasets: [{
                            data: self.data('values'),
                            backgroundColor: colors //shuffle(colors)
                        }],
                    },
        			options: {
        				responsive: true,
                        tooltips: {
                            enabled: false,
                        },
                        plugins: {
                            datalabels: {
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                formatter: (value, ctx) => {
                                    return value+' par jour';
                                },
                                color: '#fff',
                            }
                        }
                    }
                });
            });
            $('.linechart').each(function(){
                var self = $(this);
                var cpts = self.data('cpts');
                var dataset = [];
                $.each(self.data('values'), function(k, v){
                    dataset.push({
        				label: cpts[k].name,
        				borderColor: cpts[k].color,
        				backgroundColor: cpts[k].color,
        				fill: false,
                        borderWidth: 5,
        				data: v,
                    });
                });
                var ctx = document.getElementById(self.attr('id')).getContext('2d');
                var myLineChart = new Chart.Line(ctx, {
                    data: {
                        labels: self.data('labels'),
                        datasets: dataset,
                    },
                    options:{
                        plugins: {
                            datalabels: false
                        }
                    }
                });
            });
            $('.bar-tomtom').each(function(){
                var self = $(this);
                var ctx = document.getElementById(self.attr('id')).getContext('2d');
                var bg1 = "#842029";
                var bg2 = "#58151c";
                var myBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: self.data('labels'),
                        datasets: [{
                            label: self.data('label'),
                            backgroundColor: bg1,
                            hoverBackgroundColor: bg2,
                            borderWidth:1,
                            borderSkipped: 'right',
                            data: self.data('values'),
                        }]
                    },
                    options: {
                        scales: {
                            yAxes:[{
                                ticks:{
                                    max: 100,
                                    beginAtZero:true
                                }
                            }]
                        },
                        legend: {
                            display: false,
                        },
                        plugins: {
                            datalabels: false
                        },
                    }
                });
            });
            $('.bar-stack').each(function(){
                var self = $(this);
                var ctx = document.getElementById(self.attr('id')).getContext('2d');
                var bg1 = "#cae26e";
                var bg2 = "#75cbb7";
                var myBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: self.data('labels'),
                        datasets: [{
                            label: self.data('label'),
                            backgroundColor: bg1,
                            hoverBackgroundColor: bg2,
                            borderWidth:1,
                            borderSkipped: 'right',
                            data: self.data('values'),
                        }]
                    },
                    options: {
                        legend: {
                            display: false,
                        },
                        plugins: {
                            datalabels: false
                        },
                        annotation: {
                            annotations: [{
                                type: 'line',
                                mode: 'horizontal',
                                scaleID: 'y-axis-0',
                                value: 0,
                                endValue: self.data('max'),
                                borderColor: 'gray',
                                borderWidth: 3,
                                borderDash: [2, 2],
                            }],
                            drawTime: "afterDraw" // (default)
                        }
                    }
                });
            });
            $('.bar').each(function(){
                var self = $(this);
                var ctx = document.getElementById(self.attr('id')).getContext('2d');
                var myBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: self.data('labels'),
                        datasets: [{
                            label: self.data('label'),
                            barPercentage: 0.5,
                            barThickness: 6,
                            maxBarThickness: 8,
                            minBarLength: 2,
                            backgroundColor:["rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)","rgba(255, 99, 132, 0.2)","rgba(255, 159, 64, 0.2)","rgba(255, 205, 86, 0.2)","rgba(75, 192, 192, 0.2)","rgba(54, 162, 235, 0.2)","rgba(153, 102, 255, 0.2)","rgba(201, 203, 207, 0.2)"],
                            borderColor:["rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)","rgb(255, 99, 132)","rgb(255, 159, 64)","rgb(255, 205, 86)","rgb(75, 192, 192)","rgb(54, 162, 235)","rgb(153, 102, 255)","rgb(201, 203, 207)"],
                            borderWidth:1,
                            data: self.data('values'),
                        }]
                    },
                    options: {
                        scales: {
                            yAxes:[{
                                ticks:{
                                    max: self.data('max'),
                                    beginAtZero:true
                                }
                            }]
                        },
                        plugins: {
                            datalabels: false
                        },
                        annotation: {
                            annotations: [{
                                type: 'line',
                                mode: 'horizontal',
                                scaleID: 'y-axis-0',
                                value: self.data('global-avg'),
                                borderColor: 'gray',
                                borderWidth: 3,
                                label: {
                                    enabled: true,
                                    content: 'moy. : '+self.data('global-avg'),
                                    backgroundColor: 'rgba(0,0,0,0.3)',
                                    position: "end",
                                    font: {
                                        size: 7,
                                    }
                                }
                            }],
                            drawTime: "afterDraw" // (default)
                        }
                    }
                });
            });
        })
    </script>
    <?php require_once('./parts/matomo.php') ?>
</body>
</html>
