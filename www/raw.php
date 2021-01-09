<?php
require_once('../vendor/autoload.php');
use App\Compteurs;
$compteurs = (Compteurs::getInstance())->getCompteurs();
$dates = [];
$chartData = [];
$totems = [];
$totemsColors = [
    'XTH19101158'   => "#336699",
    'X2H19070220'   => "#996633",
    'X2H20042633'   => "#663399",
    'X2H20042632'   => "#FF8800",
    'X2H20063164'   => "#0088FF",
    'X2H20063163'   => "#000000",
    'X2H20063162'   => "#AA99CC",
    'X2H20042634'   => "#8800FF",
    'X2H20042635'   => "#AA55CC",
    'X2H20063161'   => "#BB66FF",
];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <style>
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
    </style>
</head>
<body>
    <section id="dl" class="row">
        <div class="col-12 text-center">
            <a href="./raw-dl.php" download class="btn btn-primary">Télécharger les données (CSV)</a>
            <a href="./meteo-dl.php" download class="btn btn-dark">Télécharger les températures (CSV)</a>
            <a href="#infometeo" class="badge rounded-pill bg-secondary" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="infometeo">
                <span class="fa fa-question">?</span>
            </a>
            <div class="collapse" id="infometeo" style="background: #ffe; text-align: left">
                <h6>Doc Météo</h6>
                <p>Les données météos sont issue du format SYNOP de Météo France : <a href="ftp://esurfmar.meteo.fr/pub/pb/tmp/doc_util_bdmo-v13.pdf" target="_blank">documentation</a> et <a href="https://donneespubliques.meteofrance.fr/client/document/doc_parametres_synop_168.pdf" target="_blank">autre documentation</a><br>Description des colonnes : </p>
                <ul>
                    <li><b>date</b> : date du relevé</li>
                    <li><b>temp. 06h00</b> : température à 06h, avec °C derrière</li>
                    <li><b>temp. 12h00</b> : température à 12h, avec °C derrière</li>
                    <li><b>temp. 18h00</b> : température à 18h, avec °C derrière</li>
                    <li><b>temp. 06h00 (brut)</b> : température à 06h, sans °C derrière</li>
                    <li><b>temp. 12h00 (brut)</b> : température à 12h, sans °C derrière</li>
                    <li><b>temp. 18h00 (brut)</b> : température à 18h, sans °C derrière</li>
                    <li><b>rr3 (06h)</b> : valeur rr3 (précipitations dans les 3 heures pécédentes) à 6h</li>
                    <li><b>rr6 (06h)</b> : valeur rr3 (précipitations dans les 6 heures pécédentes) à 6h</li>
                    <li><b>rr12 (06h)</b> : valeur rr3 (précipitations dans les 12 heures pécédentes) à 6h</li>
                    <li><b>rr24 (06h)</b> : valeur rr3 (précipitations dans les 24 heures pécédentes) à 6h</li>
                    <li><b>ff (06h)</b> : valeur ff (force du vent) à 6h</li>
                    <li><b>dd (06h)</b> : valeur dd (direction du vent) à 6h</li>
                    <li><b>cod_tend (06h)</b> : valeur cod_tend (type tendance baro) à 6h</li>
                    <li><b>rr3 (12h)</b> : valeur rr3 (précipitations dans les 3 heures pécédentes) à 12h</li>
                    <li><b>rr6 (12h)</b> : valeur rr3 (précipitations dans les 6 heures pécédentes) à 12h</li>
                    <li><b>rr12 (12h)</b> : valeur rr3 (précipitations dans les 12 heures pécédentes) à 12h</li>
                    <li><b>rr24 (12h)</b> : valeur rr3 (précipitations dans les 24 heures pécédentes) à 12h</li>
                    <li><b>ff (12h)</b> : valeur ff (force du vent) à 12h</li>
                    <li><b>dd (12h)</b> : valeur dd (direction du vent) à 12h</li>
                    <li><b>cod_tend (12h)</b> : valeur cod_tend (type tendance baro) à 12h</li>
                    <li><b>rr3 (18h)</b> : valeur rr3 (précipitations dans les 3 heures pécédentes) à 18h</li>
                    <li><b>rr6 (18h)</b> : valeur rr3 (précipitations dans les 6 heures pécédentes) à 18h</li>
                    <li><b>rr12 (18h)</b> : valeur rr3 (précipitations dans les 12 heures pécédentes) à 18h</li>
                    <li><b>rr24 (18h)</b> : valeur rr3 (précipitations dans les 24 heures pécédentes) à 18h</li>
                    <li><b>ff (18h)</b> : valeur ff (force du vent) à 18h</li>
                    <li><b>dd (18h)</b> : valeur dd (direction du vent) à 18h</li>
                    <li><b>cod_tend (18h)</b> : valeur cod_tend (type tendance baro) à 18h</li>
                </ul>
                <p>Nota : l'absence de donnée est remplaceé par "-"</p>
            </div>
        </div>
    </section>
    <section id="main" class="container">
        <div class="row">
            <?php if(count($compteurs) > 0): ?>
                <?php foreach($compteurs as $k => $compteur): ?>
                    <?php $data = $compteur->get('dataTotal') ?>
                    <?php $chartData[$k] = [] ?>
                    <?php $totems[$k] = $compteur->get('label') ?>
                    <div class="col-6">
                        <section class="compteur" id="compteur-<?php echo $compteur->get('slug') ?>">
                            <h2>
                                <a href="#compteur-<?php echo $compteur->get('slug') ?>">
                                    <?php echo $compteur->get('labelHTML') ?>
                                </a>
                            </h2>
                            <div class="recap">
                                <ul>
                                    <li>
                                        <strong>Cette année <?php echo date('Y') ?></strong>
                                        <ul>
                                            <li>
                                                Total : <?php echo $compteur->get('sumCurYear') ?>
                                            </li>
                                            <li>
                                                Moyenne : <?php echo $compteur->get('avgCurYear') ?>
                                            </li>
                                            <li>
                                                Record : <?php echo $compteur->get('recordYear') ?> (<?php echo $compteur->get('recordYearDate') ?>)
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <strong>Toutes les données</strong>
                                        <ul>
                                            <li>
                                                Total : <?php echo $compteur->get('sumTotal') ?>
                                            </li>
                                            <li>
                                                Moyenne : <?php echo $compteur->get('avgTotal') ?>
                                            </li>
                                            <li>
                                                Record : <?php echo $compteur->get('recordTotal') ?> (<?php echo $compteur->get('recordTotalDate') ?>)
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <?php if(is_array($data) && count($data) > 0): ?>
                                <strong>Données brutes</strong>
                                <ul>
                                    <?php foreach($data as $date => $val): ?>
                                        <li><?php echo $date ?> : <b><?php echo $val ?></b></li>
                                        <?php $chartData[$k][$date] = $val ?>
                                    <?php endforeach ?>
                                    <?php $dates = array_merge($dates, $compteur->get('chartDates', 10000)) ?>
                                </ul>
                            <?php endif ?>
                        </section>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        </div>
        <?php /* ?>
        <div class="col-7">
            <canvas id="chart" style="width:90%;" height="400"></canvas>
            <div class="row" style="margin-top:50px; text-align:center">
                <a href="./raw-dl.php" download class="btn btn-primary">Télécharger les données (CSV)</a>
            </div>
        </div>
        <?php /* */ ?>
    </section>
    <?php /* ?>
<?php if(count($chartData) > 0): ?>
    <?php $dates = array_unique($dates) ?>
    <?php foreach($dates as $k => $d){ $dates[$k] = '"'.$d.'"'; } ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script type="text/javascript">
        var ctx = document.getElementById('chart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [<?php echo implode(',', $dates) ?>],
                datasets: [
                    <?php foreach($chartData as $k => $val): ?>
                        <?php
                            foreach($dates as $date){
                                if(!isset($val[$date])){
                                    $val[$date] = 0;
                                }
                            }
                        ?>
                        {
                            label: "<?php echo $totems[$k] ?>",
                            data: [<?php echo implode(',', $val) ?>],
                            fill: false,
                            borderColor: "<?php echo $totemsColors[$k] ?>",
                        },
                    <?php endforeach ?>
                ]
            }
        });
    </script>
<?php endif ?>
<?php /* */ ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>
</html>
