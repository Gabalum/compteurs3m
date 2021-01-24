<?php
namespace App;
require_once('../bootstrap.php');
$compteurs = (Compteurs::getInstance())->getCompteurs();
$days = (isset($_GET['days']) ? intval($_GET['days']) : 0);
$s1 = null;
if($days < 3 || $days > 8){
    $days = 3;
}else{
    $s1 = $days;
}
$s2 = null;
$days2 = (isset($_GET['days2']) ? intval($_GET['days2']) : 0);
if($days2 < 3 || $days2 > 8){
    $days2 = 3;
}else{
    $s2 = $days2;
}
$avg = [];
$avg2 = [];
foreach($compteurs as $k => $cpt){
    $data = $cpt->get('dataCurYear');
    $i = 0;
    $avg[$k] = [];
    $avg2[$k] = [];
    if(is_array($data) && count($data) > 0){
        foreach($data as $ts => $item){
            $x[$i] = $item['value'];
            if($i > ($days-2)){
                $sum = 0;
                for($z = 0 ; $z < $days; $z++){
                    $sum += $x[$i-$z];
                }
                $avg[$k][$item['date']] = $sum/$days;
            }
            if($i > ($days2-2)){
                $sum = 0;
                for($z = 0 ; $z < $days2; $z++){
                    $sum += $x[$i-$z];
                }
                $avg2[$k][$item['date']] = $sum/$days2;
            }
            $i++;
        }
    }
}
?><html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Compteurs : moyennes mobiles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <style>
        .legend{display: flex; align-items: center;}
    </style>
</style>
</head>
<body>
    <h1 class="text-center">Moyennes mobiles</h1>
    <section id="form" class="container-lg text-center">
        <div class="row">
            <div class="col-2 align-middle legend"><b>Lissage : </b></div>
            <form class="row g-3 col-10">
                <div class="col-auto">
                    <label for="days" class="visually-hidden">Nombre de jours : </label>
                    <select name="days" id="days" class="form-control">
                        <option value="3" <?php echo (is_null($s1) ? 'selected' : '') ?>>-- Nombre de jours</option>
                        <?php for($i = 3; $i < 8; $i++): ?>
                            <option value="<?php echo $i ?>" <?php echo ($s1 == $i ? 'selected' : '') ?>><?php echo $i ?> jours</option>
                        <?php endfor ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label for="days2" class="visually-hidden">Nombre de jours (comparatif) : </label>
                    <select name="days2" id="days2" class="form-control">
                        <option value="3" <?php echo (is_null($s2) ? 'selected' : '') ?>>-- Nombre de jours (compare)</option>
                        <?php for($i = 3; $i < 8; $i++): ?>
                            <option value="<?php echo $i ?>" <?php echo ($s2 == $i ? 'selected' : '') ?>><?php echo $i ?> jours</option>
                        <?php endfor ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-3">Valider</button>
                </div>
            </form>
        </div>
    </section>
    <?php if(count($compteurs) > 0): ?>
        <section id="compteurs" class="container-lg">
            <?php foreach($compteurs as $k => $c): ?>
                <article id="cpt-<?php echo $k ?>" class="row">
                    <h2><?php echo $c->get('labelHTML') ?></h2>
                    <?php if($days !== $days2): ?>
                        <div class="col-12 col-md-6">
                            <b>Lissage sur <?php echo $days ?> jours</b>
                            <canvas id="line-<?php echo $k ?>" class="line" data-labels='<?php echo json_encode(array_keys($avg[$k])) ?>' data-values='<?php echo json_encode(array_values($avg[$k])) ?>' data-max="<?php echo max(array_values($avg[$k])) ?>"></canvas>
                        </div>
                        <div class="col-12 col-md-6">
                            <b>Lissage sur <?php echo $days2 ?> jours</b>
                            <canvas id="line2-<?php echo $k ?>" class="line line2" data-labels='<?php echo json_encode(array_keys($avg2[$k])) ?>' data-values='<?php echo json_encode(array_values($avg2[$k])) ?>' data-max="<?php echo max(array_values($avg2[$k])) ?>"></canvas>
                        </div>
                    <?php else: ?>
                        <div>
                            <b>Lissage sur <?php echo $days ?> jours</b>
                            <canvas id="line-<?php echo $k ?>" class="line" data-labels='<?php echo json_encode(array_keys($avg[$k])) ?>' data-values='<?php echo json_encode(array_values($avg[$k])) ?>' data-max="<?php echo max(array_values($avg[$k])) ?>"></canvas>
                        </div>
                    <?php endif ?>
                </article>
            <?php endforeach ?>
        </section>
    <?php endif ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@0.5.7/chartjs-plugin-annotation.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script type="text/javascript">
        $('document').ready(function(){
            $('.line').each(function(){
                var self = $(this);
                var ctx = document.getElementById(self.attr('id')).getContext('2d');
                var bg = self.hasClass('line2') ? '#cae26e' : '#fff3cd';
                var bd = self.hasClass('line2') ? '#75cbb7' : '#ffda6a';
                var myLineChart = new Chart.Line(ctx, {
                    data: {
                        labels: self.data('labels'),
                        datasets: [{
                            data: self.data('values'),
                            backgroundColor: bg,
                            borderColor: bd,
                        }],
                    },
                    options:{
                        legend: {
                            display: false,
                        },
                        scales: {
                            yAxes:[{
                                ticks:{
                                    max: self.data('max')+200,
                                    beginAtZero:true,
                                    stepSize: 200,
                                }
                            }],
                        },
                        plugins: {
                            datalabels: false
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
