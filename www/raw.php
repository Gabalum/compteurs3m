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
</body>
</html>
