<?php
namespace App;
require_once('../bootstrap.php');
$albert = (Albert::getInstance())->getData();
//var_dump($albert);die();
if(is_null($albert)){
    header('HTTP/1.0 404 Not Found');
    require_once('./404.php');
    exit;
}
$title = "Relevés Albert 1er par la communauté | Les compteurs de Montpellier 3M";
$desc = 'Les relevés des données du compteur Albert 1er effectués par la communauté des cyclistes montpelliérains';
$imgSocial = _BASE_URL_.'assets/img/albert-fb.jpg';
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
<body class="albert">
    <section id="head" class="container-lg clearfix">
        <h1>Relevés communautaires du totem Albert 1<sup>er</sup></h1>
        <div class="float-end">
            <a class="btn btn-totem" href="<?php echo _BASE_URL_ ?>detail/albert-1er">
                Retour au totem
            </a>
            <a class="btn btn-totem" href="<?php echo _BASE_URL_ ?>">
                Tous les compteurs
            </a>
        </div>
    </section>
    <section id="main" class="container-lg">
        <div class="row">
            <div class="col-12">
                <?php if(count($albert) > 0): ?>
                    <ul class="nav nav-tabs nav-tabs-pm" id="tabs-albert-controls" role="tablist">
                        <?php foreach($albert as $year => $values): ?>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?php if($year === _YEAR_): ?>active<?php endif ?>" id="link-albert-<?php echo $year ?>" data-bs-toggle="tab" href="#tab-<?php echo $year ?>" role="tab" aria-controls="tab-<?php echo $year ?>" aria-selected="<?php echo ($year === _YEAR_ ? 'true' : 'false' ) ?>">
                                    <?php echo $year ?>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                    <div class="tab-content" id="tabs-albert">
                        <?php foreach($albert as $year => $values): ?>
                            <?php krsort($values) ?>
                            <div class="tab-pane fade <?php if($year === _YEAR_): ?>show active<?php endif ?>" id="tab-<?php echo $year ?>" role="tabpanel" aria-labelledby="tab-<?php echo $year ?>">
                                <table class="text-center table table-light table-oddeven align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">Date</th>
                                            <th scope="col">Heure</th>
                                            <th scope="col">Valeur</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($values as $k => $dates): ?>
                                            <?php $class = ($k++%2 == 0 ? 'odd' : 'even') ?>
                                            <?php foreach($dates as $i => $item): ?>
                                                <tr class="<?php echo $class ?>">
                                                    <?php if($i == 0): ?>
                                                        <th scope="row" class="th-<?php echo $k ?>" <?php if(count($dates) > 1): ?>rowspan="<?php echo count($dates) ?>"<?php endif ?>>
                                                            <?php echo $item['jour'] ?><br>
                                                            <?php echo $item['date'] ?>
                                                            <?php if($item['isFerie']): ?><br><em>jour férié</em><?php endif ?>
                                                        </th>
                                                    <?php endif ?>
                                                    <td data-target=".th-<?php echo $k ?>"><?php echo $item['heure'] ?></td>
                                                    <td data-target=".th-<?php echo $k ?>"><?php echo $item['instant'] ?></td>
                                                    <?php if($i == 0): ?>
                                                        <th  class="th-<?php echo $k ?>" <?php if(count($dates) > 1): ?>rowspan="<?php echo count($dates) ?>"<?php endif ?>>
                                                            <?php echo $item['totalJour'] ?>
                                                        </th>
                                                    <?php endif ?>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                                <?php /* ?>
                                <div class="charts">
                                    <canvas id="bar-monthes-<?php echo $year ?>"
                                        class="bar bar-monthes"
                                        data-year="<?php echo $year ?>"
                                        data-labels='<?php echo json_encode(array_column($monthes[$year], 'name')) ?>'
                                        data-values='<?php echo json_encode($monthes[$year]) ?>'
                                    ></canvas>
                                </div>
                                <?php /* */ ?>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>
        </div>
        <div class="row text-center">
            <p>Ces données ont été saisies manuellement par les cyclistes de Montpellier et alentours.</p>
            <br>
            <div class="col-2"></div>
            <div class="col-8">
            <a href="https://docs.google.com/forms/d/e/1FAIpQLSfPHrWpHSj0A0VHzkaBlvSYCgUBQQyQOPOJ6lhq0dIDLvcDlg/viewform" target="_blank" class="btn btn-primary">
                Ajouter une saisie
            </a>
            <a href="https://docs.google.com/spreadsheets/d/e/2PACX-1vQVtdpXMHB4g9h75a0jw8CsrqSuQmP5eMIB2adpKR5hkRggwMwzFy5kB-AIThodhVHNLxlZYm8fuoWj/pub?gid=2105854808&single=true&output=csv" download class="btn btn-primary">
                Télécharger les données
            </a>
            <div class="col-2"></div>
        </div>
        </div>
    </section>
    <footer>
        <div class="container">
            <?php require_once('./parts/note-footer.php') ?>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="<?php echo Helper::noCache('./assets/js/main.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.table-oddeven').find('td').mouseover(function(){
                $($(this).data('target')).addClass('thover');
            });
            $('.table-oddeven').find('td').mouseout(function(){
                $('.thover').removeClass('thover');
            });
<?php /*  ?>
            $('.bar').each(function(){
                var self = $(this);
                var ctx = document.getElementById(self.attr('id')).getContext('2d');
                var data10 = [];
                var data14 = [];
                var data18 = [];
                var dataTot = [];
                var values = self.data('values');
                $.each(values, function(k, v){
                    data10.push(v[10].avg),
                    data14.push(v[14].avg),
                    data18.push(v[18].avg),
                    dataTot.push(v.total.avg)
                });
                console.log(self.data('labels'));
                var myBarChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: self.data('labels'),
                        datasets: [{
                            label: 'avant 10h',
                            backgroundColor: '#336699',
                            data: data10
                        },{
                            label: 'avant 14h',
                            backgroundColor: '#FF8800',
                            data: data14
                        },{
                            label: 'avant 18h',
                            backgroundColor: '#0088FF',
                            data: data18
                        },{
                            label: 'après 18h',
                            backgroundColor: '#FF0000',
                            data: dataTot
                        },
                        ]
                    },
    				options: {
    					title: {
    						display: true,
    						text: 'Moyenne quotidienne, par mois, en '+self.data('year')
    					},
    					tooltips: {
    						mode: 'index',
    						intersect: false
    					},
    					responsive: true,
    					scales: {
    						xAxes: [{
    							stacked: true,
    						}],
    						yAxes: [{
    							stacked: true
    						}]
    					}
    				}
                });
            });
<?php /* */ ?>
        });
    </script>
</body>
</html>
