<?php
    namespace App;
    require_once(dirname(__DIR__, 2).'/bootstrap.php');
    $quantieme = intval(((date('z')+1)/365)*100);
    $slug = (isset($_GET['cpt']) ? strip_tags($_GET['cpt']) : '');
    $compteur = (Compteurs::getInstance())->getCompteurBySlug($slug);
    if(is_null($compteur)){
        $slug = "albert-1er";
        $compteur = (Compteurs::getInstance())->getCompteurBySlug($slug);
    }
    $rowData = (Compteurs::getInstance())->getAllByDates();
    $cptLabels = (Compteurs::getInstance())->getLabels();
    $yesterday = (new \DateTime())->modify('-1 day')->format('d-m-Y');
    $data = $compteur->get('dataTotalWithCplt');
    $monthes = $compteur->get('monthes');
    $monthesY = $compteur->get('monthesY');
    $currentMonth = (is_array($monthes) && isset($monthes[date('m')]) ? $monthes[date('m')] : null);
    $latestColor = ($compteur->get('lastDate') == $yesterday ? 'text-green-600' : 'text-red-600');
    $days = $compteur->get('days-by-year');
    $weeksY = $compteur->get('weeksY');
    $stack = $compteur->getSumStack();
    $startYear = substr($compteur->get('firstDate'),-4);
    $maxDay = [];
    for($dow = 1 ; $dow <= 7 ; $dow++) {
        $maxDay[$dow] = 0;
        for($y = $startYear ; $y <= date('Y') ; $y++) {
            if($days[$y][$dow]['value'] > $maxDay[$dow]) {
                $maxDay[$dow] = $days[$y][$dow]['value'];
            }
        }
    }
    $worstByYear = [];
    foreach($monthesY as $y => $mm){
        $vals = array_column($mm, 'worst');
        foreach($vals as $k => $v){
            if(is_null($v)){
                unset($vals[$k]);
            }
        }
        $worstByYear[$y] = min($vals);
    }
    $vals = $compteur->get('dataTotalValues');
    $items = [];
    $curMonthVals = [];
    $curYearVals = [];
    foreach($vals as $k => $v){
        if((int)substr($k, 0, 4) === (_YEAR_-1) && (int)substr($k, 4, 2) <= (int)date('m')){
            $items[$k] = $v;
        }
        if((int)substr($k, 0, 4) === _YEAR_ && (int)substr($k, 4, 2) === (int)date('m')) {
            $curMonthVals[] = $v;
        }
        if((int)substr($k, 0, 4) === _YEAR_) {
            $curYearVals[$v] = $k;
        }
    }
    sort($curMonthVals);
    $median = $curMonthVals[floor(count($curMonthVals) / 2)];
    ksort($items);
    if(count($items) > 0){
        $cumulPreviousYearRaw = array_sum(array_slice($items, 0, date('z')));
        $maxP = max($cumulPreviousYearRaw, $compteur->get('sumCurYear')) * 3;
        $cumulPreviousYear = intval($cumulPreviousYearRaw * 100 / $maxP);
        $cumulCurrentYear = intval($compteur->get('sumCurYear') * 100 / $maxP);
        $progress = min(100, intval($compteur->get('sumCurYear') * 100 / $compteur->get('sumByYear')[(_YEAR_-1)]));
    }
    $title = $compteur->get('label').' :: Dashboard Compteurs 3M';
// ------- gestion des records
    $recordTotal = false;
    $recordYear = false;
    $recordMonth = false;
    if($compteur->get('lastValue') === $compteur->get('recordTotal')){
        $recordTotal = true;
    } elseif($compteur->get('lastValue') === $compteur->get('recordYear')){
        $recordYear = true;
    } elseif(!is_null($currentMonth) && $compteur->get('lastValue') === $currentMonth['value']){
        $recordMonth = true;
    }
// ------ gestion des rankings
    $rvals = array_flip($vals);
    krsort($rvals);
    $rankingTotal = [];
    $i = 0;
    foreach($rvals as $k => $v){
        $x = str_split($v);
        $date = $x[6].$x[7].'-'.$x[4].$x[5].'-'.$x[0].$x[1].$x[2].$x[3];
        $rankingTotal[$date] = $k;
        if($i++ >= 9){
            break;
        }
    }
    krsort($curYearVals);
    $rankingYear = [];
    $i = 0;
    foreach($curYearVals as $k => $v){
        $x = str_split($v);
        $date = $x[6].$x[7].'-'.$x[4].$x[5].'-'.$x[0].$x[1].$x[2].$x[3];
        $rankingYear[$date] = $k;
        if($i++ >= 9){
            break;
        }
    }
    $rankingWeek = [];
    $rankingWeekY = [];
    $mt = $compteur->get('medianTotal');
    $dw = (int)date('W');
    $latestWeek = 0;
    foreach($weeksY as $year => $weeks){
        foreach($weeks as $nb => $week){
            if($week['sum'] > $mt){
                if($year === _YEAR_ && (int)$nb === $dw){

                }else{
                    $rankingWeek[] = [
                        'num'   => (int)$nb,
                        'year'  => $year,
                        'sum'   => $week['sum']
                    ];
                    if($year === _YEAR_){
                        $rankingWeekY[] = [
                            'num'   => (int)$nb,
                            'year'  => $year,
                            'sum'   => $week['sum']
                        ];
                    }
                }
                if($year === _YEAR_ && (int)$nb === ($dw-1)){
                    $latestWeek = $week['sum'];
                }
            }
        }
    }
    usort($rankingWeek, function($a, $b){
        return $a['sum'] < $b['sum'];
    });
    usort($rankingWeekY, function($a, $b){
        return $a['sum'] < $b['sum'];
    });
    $rankingWeek = array_slice($rankingWeek, 0, 10);
    $rankingWeekY = array_slice($rankingWeekY, 0, 10);
    require_once(dirname(__FILE__).'/parts/header.php');
?>
<header class="fixed bg-blue-900 text-white w-full flex pl-5 gap-3 text-sm pt-1">
    <div class="font-bold pb-2"><?php echo $compteur->get('labelHTML') ?></div>
    <div class="hidden lg:inline">
        | ID : <?php echo $compteur->get('id') ?>
        | <span class="<?php echo $latestColor ?>">Dernier relevé : <b><?php echo $compteur->get('lastValue') ?></b> le <?php echo $compteur->get('lastDate') ?></span>
    </div>
    <div class="">| Section :
        <a href="#a-summary">Résumé</a>&nbsp;•&nbsp;
        <a href="#a-jour">Par jour</a>&nbsp;•&nbsp;
        <a href="#a-semaine">Par semaine</a>&nbsp;•&nbsp;
        <a href="#a-mois">Par mois</a>&nbsp;•&nbsp;
        <a href="#a-stats">Stats</a>
    </div>
</header>
<main class="w-full flex-grow p-6 pb-20">
    <div id="a-summary" class="pb-10"></div>
    <?php if($recordTotal): ?>
        <?php echo DashboardHelper::alert('Nouveau record absolu !', 'green', '#ranking', 'voir le top 10') ?>
    <?php elseif($recordYear): ?>
        <?php echo DashboardHelper::alert("Nouveau record de l'année !", 'blue', '#ranking', 'voir le top 10') ?>
    <?php elseif($recordMonth): ?>
        <?php echo DashboardHelper::alert("Nouveau record du mois !", 'amber', '#ranking', 'voir le top 10') ?>
    <?php endif ?>
    <section id="summary" class="bg-gradient-to-b from-slate-700 to-slate-600 py-5 px-2">
        <div class="bg-slate-900 text-white p-1 rounded-xl m-1 text-center text-lg">
            <?php echo $compteur->get('labelHTML') ?> - dernier relevé : <?php echo $compteur->get('lastValue') ?> le <?php echo $compteur->get('lastDate') ?>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
            <div class="bg-slate-900 text-white p-4 rounded-xl">
                <div class="text-center font-bold">Toutes les données</div>
                <ul>
                    <li class="grid grid-cols-2">
                        <div class="text-right pr-2">Total :</div>
                        <div><?php echo Helper::nf($compteur->get('sumTotal')) ?> / <?php echo count($compteur->get('dataTotal')) ?> jours</div>
                    </li>
                    <li class="grid grid-cols-2">
                        <div class="text-right pr-2">Moyenne :</div>
                        <div><?php echo Helper::nf($compteur->get('avgTotal')) ?> / jour</div>
                    </li>
                    <li class="grid grid-cols-2">
                        <div class="text-right pr-2">Médiane :</div>
                        <div><?php echo Helper::nf($compteur->get('medianTotal')) ?> / jour</div>
                    </li>
                    <li class="grid grid-cols-2 <?php echo ($recordTotal ? 'text-green-600' : '') ?>">
                        <div class="text-right pr-2">Record :</div>
                        <div><?php echo Helper::nf($compteur->get('recordTotal')) ?> le <?php echo $compteur->get('recordTotalDate') ?></div>
                    </li>
                    <li class="grid grid-cols-2 <?php echo ($compteur->get('worstTotal') == $compteur->get('lastValue') ? 'text-orange-900' : '') ?>">
                        <div class="text-right pr-2">Pire :</div>
                        <div><?php echo Helper::nf($compteur->get('worstTotal')) ?> le <?php echo $compteur->get('worstTotalDate') ?></div>
                    </li>
                </ul>
            </div>
            <div class="bg-slate-900 text-white p-4 rounded-xl">
                <div class="text-center font-bold">Cette année <?php echo date('Y') ?></div>
                <ul>
                    <li class="grid grid-cols-2">
                        <div class="text-right pr-2">Total :</div>
                        <div><?php echo Helper::nf($compteur->get('sumCurYear')) ?> / <?php echo count($compteur->get('dataCurYear')) ?> jour<?php echo ($compteur->get('dataCurYear') > 1 ? 's' : '') ?></div>
                    </li>
                    <li class="grid grid-cols-2">
                        <div class="text-right pr-2">Moyenne :</div>
                        <div><?php echo Helper::nf($compteur->get('avgCurYear')) ?> / jour</div>
                    </li>
                    <li class="grid grid-cols-2">
                        <div class="text-right pr-2">Médiane :</div>
                        <div><?php echo Helper::nf($compteur->get('medianByYear')[_YEAR_]) ?> / jour</div>
                    </li>
                    <li class="grid grid-cols-2 <?php echo ($recordTotal ? 'text-green-600' : ($recordYear ? 'text-blue-600' : '')) ?>">
                        <div class="text-right pr-2">Record :</div>
                        <div><?php echo Helper::nf($compteur->get('recordYear')) ?> le <?php echo $compteur->get('recordYearDate') ?></div>
                    </li>
                    <li class="grid grid-cols-2 <?php echo ($compteur->get('worstYear') == $compteur->get('lastValue') ? 'text-orange-900' : '') ?>">
                        <div class="text-right pr-2">Pire :</div>
                        <div><?php echo Helper::nf($compteur->get('worstYear')) ?> le <?php echo $compteur->get('worstYearDate') ?></div>
                    </li>
                </ul>
            </div>
            <div class="bg-slate-900 text-white p-4 rounded-xl">
                <div class="text-center font-bold">Ce mois-ci <?php echo Helper::frenchMonth(date('m'), false).' '.date('Y') ?></div>
                <ul>
                    <li class="grid grid-cols-2">
                        <div class="text-right pr-2">Total :</div>
                        <div><?php echo Helper::nf($currentMonth['sum']) ?> / <?php echo $currentMonth['cpt'] ?> jour<?php echo ($currentMonth['cpt'] > 1 ? 's' : '') ?></div>
                    </li>
                    <li class="grid grid-cols-2">
                        <div class="text-right pr-2">Moyenne :</div>
                        <div><?php echo Helper::nf($currentMonth['avg']) ?> / jour</div>
                    </li>
                    <li class="grid grid-cols-2">
                        <div class="text-right pr-2">Médiane :</div>
                        <div><?php echo Helper::nf($median) ?> / jour</div>
                    </li>
                    <li class="grid grid-cols-2 <?php echo ($recordTotal ? 'text-green-600' : ($recordYear ? 'text-blue-600' : ($recordMonth ? 'text-amber-600' : ''))) ?>">
                        <div class="text-right pr-2">Record :</div>
                        <div><?php echo Helper::nf($currentMonth['value']) ?> le <?php echo $currentMonth['date'] ?></div>
                    </li>
                    <li class="grid grid-cols-2 <?php echo ($currentMonth['worst'] == $compteur->get('lastValue') ? 'text-orange-900' : '') ?>">
                        <div class="text-right pr-2">Pire :</div>
                        <div><?php echo Helper::nf($currentMonth['worst']) ?> le <?php echo $currentMonth['worstDate'] ?></div>
                    </li>
                </ul>
            </div>
        </div>
        <?php if(count($items) > 0): ?>
            <div class="flex justify-between mb-1">
                <span class="text-base font-medium text-blue-700 dark:text-white">Avancement par rapport à l'an dernier</span>
                <span class="text-sm font-medium text-blue-700 dark:text-white"><?php echo $progress ?>% (jours de l'année : <?php echo $quantieme ?>%)</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 dark:bg-gray-700" style="position:relative">
              <div style="position: absolute;border:0;border-left:2px solid red;left: <?php echo $quantieme ?>%;top: -5px;">&nbsp;</div>
              <div class="bg-blue-600 h-3 rounded-full" style="width: <?php echo $progress ?>%"></div>
            </div>
            <div class="flex justify-between mt-2 mb-1">
                <span class="text-base font-medium text-blue-700 dark:text-white">Progression par rapport à l'an dernier</span>
                <span class="text-sm font-medium text-blue-700 dark:text-white"><?php echo ($compteur->get('sumCurYear') > $cumulPreviousYearRaw?'+':'').($compteur->get('sumCurYear') - $cumulPreviousYearRaw) ?></span>
            </div>
            <div class="flex flex-col sm:flex-row text-white">
                <p class="w-full sm:w-2/12 lg:w-1/12">Année&nbsp;<?php echo (_YEAR_-1) ?></p>
                <div class="w-full sm:w-10/12 lg:w-11/12 bg-gray-200 rounded-full h-3 dark:bg-gray-700 ml-2 mt-2">
                  <div class="<?php echo($cumulPreviousYear > $cumulCurrentYear ? 'bg-green-600' : 'bg-orange-600') ?> h-3 rounded-full" style="width: <?php echo $cumulPreviousYear ?>%"></div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row text-white">
                <p class="w-full sm:w-2/12 lg:w-1/12">Année&nbsp;<?php echo _YEAR_ ?></p>
                <div class="w-full sm:w-10/12 lg:w-11/12 bg-gray-200 rounded-full h-3 dark:bg-gray-700 ml-2 mt-2">
                  <div class="<?php echo($cumulPreviousYear > $cumulCurrentYear ? 'bg-orange-600' : 'bg-green-600') ?> h-3 rounded-full" style="width: <?php echo $cumulCurrentYear ?>%"></div>
                </div>
            </div>
        <?php endif ?>
    </section>
    <div id="a-jour" class="pb-10"></div>
    <section id="donnees-jour" class="pb-10">
        <h2 class="text-3xl text-black">
            Données par jours
        </h2>
        <h3 class="text-2xl text-black">
            Records
        </h3>
        <div class="table min-w-full bg-white border border-gray-500 mb-5">
            <div class="table-header-group bg-slate-900 text-white">
                <div class="table-row flex">
                    <div class="table-cell w-1/5 text-center pt-2 pb-2">Jour</div>
                    <?php for($y = $startYear ; $y <= date('Y') ; $y++): ?>
                        <div class="table-cell text-center"><?php echo $y ?></div>
                    <?php endfor ?>
                </div>
            </div>
            <div class="table-row-group">
                <?php for($dow = 1 ; $dow <= 7 ; $dow++): ?>
                    <div class="table-row text-center <?php echo ($dow%2==0 ? 'bg-gray-200' : '') ?>">
                        <div class="table-cell text-left p-1 font-bold"><?php echo Helper::frenchDayOfTheWeek($dow) ?></div>
                        <?php for($y = $startYear ; $y <= date('Y') ; $y++): ?>
                            <?php
                                $css = '';
                                if($days[$y][$dow]['value'] === $maxDay[$dow]){
                                    $css .= ' font-bold';
                                }
                                if($y === _YEAR_){
                                    if($days[$y][$dow]['value'] === $compteur->get('lastValue')){
                                        $css .= ' text-green-600';
                                    }
                                }
                            ?>
                            <div class="table-cell">
                                <span class="<?php echo $css ?>">
                                    <?php echo $days[$y][$dow]['value'] ?>
                                </span>
                                <div class="text-sm text-gray-400">(<?php echo $days[$y][$dow]['date'] ?>)</div>
                            </div>
                        <?php endfor ?>
                    </div>
                <?php endfor ?>
            </div>
        </div>
        <h3 class="text-2xl text-black">
            Comparaison année en cours
        </h3>
        <?php $max = max(array_merge(array_column($compteur->get('days'), 'avg'), array_column($days[date('Y')], 'avg'))) ?>
        <div class="flex flex-col sm:flex-row">
            <div class="w-full sm:w-1/2 mr-3 sm:mr-0 pr-3">
                <div class="font-bold">Toutes les données</div>
                <?php $tmpDays = $compteur->get('days'); ?>
                <?php $avg = $compteur->get('avgTotal') ?>
                <?php $isCurrentY = false ?>
                <?php require(dirname(__FILE__).'/parts/table-day.php') ?>
            </div>
            <div class="w-full sm:w-1/2 mr-3 sm:mr-0 pr-3">
                <div class="font-bold">En <?php echo date('Y') ?></div>
                <?php $tmpDays = $days[date('Y')] ?>
                <?php $avg = $compteur->get('avgCurYear') ?>
                <?php $isCurrentY = true ?>
                <?php require(dirname(__FILE__).'/parts/table-day.php') ?>
            </div>
        </div>
    </section>
    <div id="a-semaine" class="pb-10"></div>
    <section id="donnees-semaines" class="pb-10">
        <h2 class="text-3xl text-black">
            Données par semaines
        </h2>
        <p class="text-xl">Scrollez horizontalement et verticalement pour tout voir
        <div class="flex h-80 overflow-y-scroll overflow-x-auto space-x-2">
            <?php foreach($weeksY as $year => $weeks): ?>
                <div class="flex-shrink-0 min-w-max w-6/12">
                    <div class="font-bold">En <?php echo $year ?></div>
                    <?php require(dirname(__FILE__).'/parts/table-week.php') ?>
                </div>
            <?php endforeach ?>
        </div>
        <div class="flex overflow-x-auto space-x-2 pt-2">
            <?php foreach($weeksY as $year => $weeks): ?>
                <div class="flex-shrink-0 min-w-max w-6/12">
                    <div class="font-bold">En <?php echo $year ?></div>
                    <div>
                        <canvas id="bar-week-<?php echo uniqid() ?>" class="bar bar-weeks" data-label="par semaine" data-labels='<?php echo json_encode(array_keys($weeks)) ?>' data-values='<?php echo json_encode(array_column($weeks, 'avg')) ?>' <?php if($year == _YEAR_): ?>data-global-avg="<?php echo $compteur->get('avgCurYear') ?>"<?php endif ?>  data-max="<?php echo intval($compteur->get('maxAvgWeeksY')*1.1) ?>"></canvas>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </section>
    <div id="a-mois" class="pb-10"></div>
    <section id="donnees-mois">
        <h2 class="text-3xl text-black">
            Données par mois
        </h2>
        <p class="text-xl">Scrollez horizontalement pour tout voir
        <div class="flex overflow-x-auto space-x-2">
            <?php foreach($monthesY as $year => $monthes): ?>
                <div class="flex-shrink-0 min-w-max w-6/12">
                    <div class="font-bold">En <?php echo $year ?></div>
                    <?php require(dirname(__FILE__).'/parts/table-month.php') ?>
                </div>
            <?php endforeach ?>
        </div>
    </section>
    <div id="a-stats" class="pb-10"></div>
    <section id="stats">
        <h2 class="text-3xl text-black">
            Données et statistiques
        </h2>
        <h3 class="text-2xl text-black">
            Semaine vs week-end
        </h3>
        <div class="grid grid-col-2 md:grid-flow-col gap-4 pb-5 auto-cols-fr">
            <div class="w-full h-fit flex-0 bg-white shadow-lg rounded-sm border border-gray-200 mt-5">
                <header class="px-5 py-4 border-b border-gray-100">
                    <h4 class="font-semibold text-gray-800">Toutes les données</h4>
                </header>
                <div class="pb-5">
                    <canvas id="pie-day-<?php echo uniqid() ?>" class="pie pie-days" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values($compteur->getWeekWeekend())) ?>'></canvas>
                </div>
            </div>
            <div class="w-full h-fit flex-0 bg-white shadow-lg rounded-sm border border-gray-200 mt-5">
                <header class="px-5 py-4 border-b border-gray-100">
                    <h4 class="font-semibold text-gray-800">En <?php echo _YEAR_ ?></h4>
                </header>
                <div class="pb-5">
                    <canvas id="pie-day2-<?php echo uniqid() ?>" class="pie pie-days2" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values($compteur->getWeekWeekend(date('Y')))) ?>'></canvas>
                </div>
            </div>
        </div>
        <h3 class="text-2xl text-black">
            Jours ouvrés vs jours chomés, week-end, fériés en <?php echo date('Y') ?>
        </h3>
        <p><em>Sont considérés comme jours ouvrés les lundi, mardi, mercredi, jeudi, vendredi, hors jours fériés.</em></p>
        <div class="grid grid-col-2 md:grid-flow-col gap-4 pb-5 auto-cols-fr">
            <?php list($jo, $jc) = $compteur->getDayByType() ?>
            <div class="w-full h-fit flex-0 bg-white shadow-lg rounded-sm border border-gray-200 mt-5">
                <header class="px-5 py-4 border-b border-gray-100">
                    <h4 class="font-semibold text-gray-800">Jours ouvrés</h4>
                </header>
                <div class="pb-5">
                    <canvas id="line-jo-<?php echo uniqid() ?>" class="line" data-labels='<?php echo json_encode(array_column($jo, 'date')) ?>' data-values='<?php echo json_encode(array_column($jo, 'value')) ?>' data-max="<?php echo max(max(array_column($jo, 'value')), max(array_column($jc, 'value'))) * 1.1 ?>"></canvas>
                </div>
            </div>
            <div class="w-full h-fit flex-1 shrink-1 bg-white shadow-lg rounded-sm border border-gray-200 mt-5">
                <header class="px-5 py-4 border-b border-gray-100">
                    <h4 class="font-semibold text-gray-800">Jours chomés, week-end, fériés</h4>
                </header>
                <div class="pb-5">
                    <canvas id="line-jc-<?php echo uniqid() ?>" class="line" data-labels='<?php echo json_encode(array_column($jc, 'date')) ?>' data-values='<?php echo json_encode(array_column($jc, 'value')) ?>' data-max="<?php echo max(max(array_column($jo, 'value')), max(array_column($jc, 'value'))) * 1.1 ?>"></canvas>
                </div>
            </div>
        </div>
        <h3 class="text-2xl text-black" id="ranking">
            Top 10
        </h3>
        <div class="grid grid-col-2 md:grid-flow-col gap-4 pb-5">
            <?php echo DashboardHelper::displayRanking($rankingTotal, 'Toutes les données', $compteur->get('lastValue')) ?>
            <?php echo DashboardHelper::displayRanking($rankingYear, 'Année '._YEAR_, $compteur->get('lastValue')) ?>
        </div>
        <h3 class="text-2xl text-black" id="ranking">
            Top 10 semaines
        </h3>
        <div class="grid grid-col-2 md:grid-flow-col gap-4 pb-5">
            <?php echo DashboardHelper::displayRanking($rankingWeek, 'Toutes les données', $latestWeek) ?>
            <?php echo DashboardHelper::displayRanking($rankingWeekY, 'Année '._YEAR_, $latestWeek, true) ?>
        </div>
        <h3 class="text-2xl text-black">
            Autre
        </h3>
        <div class="grid grid-col-2 md:grid-flow-col gap-4 pb-5 auto-cols-fr">
            <div class="w-full h-fit flex-1 shrink-1 bg-white shadow-lg rounded-sm border border-gray-200 mt-5">
                <header class="px-5 py-4 border-b border-gray-100">
                    <h4 class="font-semibold text-gray-800">Données brutes</h4>
                    <em class="text-sm text-gray-400">(Scroll au sein du tableau)</em>
                </header>
                <div class="pb-5 h-80 overflow-y-scroll">
                    <?php $max = 0 ?>
                    <?php $maxY = 0 ?>
                    <ul class="list-group my-2">
                        <?php foreach($data as $date => $val): ?>
                            <?php
                                $y = substr($date, -4);
                                if($val['value'] > $max){
                                    $class = 'bg-green-300';
                                    $max = $val['value'];
                                    if($y == _YEAR_){
                                        $maxY = $val['value'];
                                    }
                                }elseif($y == _YEAR_ && $val['value'] > $maxY){
                                    if($compteur->get('recordYear') == $val['value']){
                                        $class = 'bg-blue-500';
                                    }else{
                                        $class = 'bg-blue-300';
                                    }
                                    $maxY = $val['value'];
                                }elseif($compteur->get('worstTotal') == $val['value']){
                                    $class = 'bg-orange-500';
                                }elseif($worstByYear[$y] == $val['value']){
                                    $class = 'bg-orange-300';
                                }else{
                                    $class = '';
                                }
                            ?>
                            <li class="px-2 flex border hover:bg-gray-200 border-dotted <?php echo $class ?> <?php echo ($compteur->get('recordTotal') == $val['value'] ? 'bg-green-500' : '') ?>">
                                <div class="w-1/6"><?php echo Helper::frenchDayOfTheWeek($val['day'], true) ?></div>
                                <div class="w-2/6"><?php echo $date ?></div>
                                <div class="w-2/6"><?php echo $val['value'] ?></div>
                                <div class="w-1/6 text-gray-400"><?php if($val['isFerie']): ?><em>(férié)</em><?php endif ?></div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
                <footer class="mt-2 text-sm text-gray-400 ">
                    <ul class="pt-2 ml-5">
                        <li class="pb-1"><button class="inline-block align-bottom rounded-xl w-4 h-4 mt-1 bg-green-500">&nbsp;</button> Record absolu</li>
                        <li class="pb-1"><button class="inline-block align-bottom rounded-xl w-4 h-4 mt-1 bg-green-300">&nbsp;</button> Record absolu jusque là</li>
                        <li class="pb-1"><button class="inline-block align-bottom rounded-xl w-4 h-4 mt-1 bg-blue-500">&nbsp;</button> Record en <?php echo _YEAR_ ?></li>
                        <li class="pb-1"><button class="inline-block align-bottom rounded-xl w-4 h-4 mt-1 bg-blue-300">&nbsp;</button> Record en <?php echo _YEAR_ ?> jusque là</li>
                        <li class="pb-1"><button class="inline-block align-bottom rounded-xl w-4 h-4 mt-1 bg-orange-500">&nbsp;</button> Pire scrore absolu</li>
                        <li class="pb-1"><button class="inline-block align-bottom rounded-xl w-4 h-4 mt-1 bg-orange-300">&nbsp;</button> Pire score de l'année</li>
                        <li class="pb-1"><button class="inline-block align-bottom rounded-xl w-4 h-4 mt-1"><sup>*</sup></button> Score communautaire de remplacement</li>
                    </ul>
                </footer>
            </div>
            <div class="w-full h-fit flex-1 shrink-1 mt-5">
                <div class="bg-white shadow-lg rounded-sm border border-gray-200">
                    <header class="px-5 py-4 border-b border-gray-100">
                        <h4 class="font-semibold text-gray-800">Valeurs incrémentales</h4>
                        <em class="text-sm text-gray-400">Pour l'année <?php echo _YEAR_ ?></em>
                    </header>
                    <div class="pb-5">
                        <canvas id="stack-<?php echo uniqid() ?>" class="bar-stack" data-labels='<?php echo json_encode(array_keys($stack)) ?>' data-values='<?php echo json_encode(array_values($stack)) ?>' data-max="<?php echo max($stack) ?>"></canvas>
                    </div>
                </div>
                <div class="bg-white shadow-lg rounded-sm border border-gray-200 my-5 pb-5">
                    <header class="px-5 py-4 border-b border-gray-100">
                        <h4 class="font-semibold text-gray-800">Valeurs par années</h4>
                    </header>
                    <div class="table min-w-full bg-white border border-gray-500">
                        <div class="table-header-group bg-slate-900 text-white">
                            <div class="table-row flex">
                                <div class="table-cell w-1/4 text-center pt-2 pb-2">Année</div>
                                <div class="table-cell w-1/4 text-center pt-2 pb-2">Total</div>
                                <div class="table-cell w-1/4 text-center pt-2 pb-2">Moyenne</div>
                            </div>
                        </div>
                        <div class="table-row-group">
                            <?php foreach($compteur->get('sumByYear') as $y => $v): ?>
                                <div class="table-row flex text-center <?php echo ($y%2==0 ? 'bg-gray-200' : '') ?>">
                                    <div class="table-cell font-bold">
                                        <?php echo $y ?>
                                    </div>
                                    <div class="table-cell"><?php echo Helper::nf($v) ?></div>
                                    <div class="table-cell"><?php echo (isset($compteur->get('medianByYear')[$y]) ? Helper::nf($compteur->get('medianByYear')[$y]) : '-') ?></div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require_once(dirname(__FILE__).'/parts/footer.php') ?>
