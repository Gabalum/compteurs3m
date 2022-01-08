<?php
    namespace App;
    require_once(dirname(__DIR__, 2).'/bootstrap.php');
    $slug = (isset($_GET['cpt']) ? strip_tags($_GET['cpt']) : '');
    $compteur = (Compteurs::getInstance())->getCompteurBySlug($slug);
    if(is_null($compteur)){
        $slug = "albert-1er";
        $compteur = (Compteurs::getInstance())->getCompteurBySlug($slug);
    }
    $rowData = (Compteurs::getInstance())->getAllByDates();
    $cptLabels = (Compteurs::getInstance())->getLabels();
    $yesterday = (new \DateTime())->modify('-1 day')->format('d-m-Y');
    $data = $compteur->get('dataTotal');
    $monthes = $compteur->get('monthes');
    $monthesY = $compteur->get('monthesY');
    $currentMonth = (is_array($monthes) && isset($monthes[date('m')]) ? $monthes[date('m')] : null);
    $latestColor = ($compteur->get('lastDate') == $yesterday ? 'text-green-200' : 'text-red-200');
    $days = $compteur->get('days-by-year');
    $weeksY = $compteur->get('weeksY');
    $stack = $compteur->getSumStack();
    $maxDay = [];
    for($dow = 1 ; $dow <= 7 ; $dow++) {
        $maxDay[$dow] = 0;
        for($y = 2020 ; $y <= date('Y') ; $y++) {
            if($days[$y][$dow]['value'] > $maxDay[$dow]) {
                $maxDay[$dow] = $days[$y][$dow]['value'];
            }
        }
    }
    require_once(dirname(__FILE__).'/parts/header.php');
?>
<header class="fixed bg-blue-900 text-white w-full flex pl-5 gap-3">
    <div class="font-bold pb-2"><?php echo $compteur->get('labelHTML') ?></div>
    <div class="">
        <span class="hidden lg:inline">| ID : <?php echo $compteur->get('id') ?></span>
        | <span class="<?php echo $latestColor ?>">Dernier relevé : <b><?php echo $compteur->get('lastValue') ?></b> le <?php echo $compteur->get('lastDate') ?></span>
    </div>
    <div class="">| Section :
        <a href="#a-summary">Records</a>&nbsp;•&nbsp;
        <a href="#a-jour">Par jour</a>&nbsp;•&nbsp;
        <a href="#a-semaine">Par semaine</a>&nbsp;•&nbsp;
        <a href="#a-mois">Par mois</a>&nbsp;•&nbsp;
        <a href="#a-stats">Stats</a>
    </div>
</header>
<main class="w-full flex-grow p-6 pb-20">
    <div id="a-summary" class="pb-10"></div>
    <section id="summary" class="grid grid-cols-3 gap-6 mb-6 bg-gradient-to-b from-slate-700 to-slate-600 py-5 px-2">
        <div class="bg-slate-900 text-white p-4 rounded-xl">
            <div class="text-center font-bold">Toutes les données</div>
            <ul>
                <li class="flex"><div class="w-1/3 text-right pr-2">Total :</div> <?php echo $compteur->get('sumTotal') ?> / <?php echo count($compteur->get('dataTotal')) ?> jours</li>
                <li class="flex"><div class="w-1/3 text-right pr-2">Moyenne :</div> <?php echo $compteur->get('avgTotal') ?> / jour</li>
                <li class="flex"><div class="w-1/3 text-right pr-2">Record :</div> <?php echo $compteur->get('recordTotal') ?> le <?php echo $compteur->get('recordTotalDate') ?></li>
            </ul>
        </div>
        <div class="bg-slate-900 text-white p-4 rounded-xl">
            <div class="text-center font-bold">Cette année <?php echo date('Y') ?></div>
            <ul>
                <li class="flex"><div class="w-1/3 text-right pr-2">Total :</div> <?php echo $compteur->get('sumCurYear') ?> / <?php echo count($compteur->get('dataCurYear')) ?> jour<?php echo ($compteur->get('dataCurYear') > 1 ? 's' : '') ?></li>
                <li class="flex"><div class="w-1/3 text-right pr-2">Moyenne :</div> <?php echo $compteur->get('avgCurYear') ?> / jour</li>
                <li class="flex"><div class="w-1/3 text-right pr-2">Record :</div> <?php echo $compteur->get('recordYear') ?> le <?php echo $compteur->get('recordYearDate') ?></li>
            </ul>
        </div>
        <div class="bg-slate-900 text-white p-4 rounded-xl">
            <div class="text-center font-bold">Ce mois-ci <?php echo date('Y') ?></div>
            <ul>
                <li class="flex"><div class="w-1/3 text-right pr-2">Total :</div> <?php echo $currentMonth['sum'] ?> / <?php echo $currentMonth['cpt'] ?> jour<?php echo ($currentMonth['cpt'] > 1 ? 's' : '') ?></li>
                <li class="flex"><div class="w-1/3 text-right pr-2">Moyenne :</div> <?php echo $currentMonth['avg'] ?> / jour</li>
                <li class="flex"><div class="w-1/3 text-right pr-2">Record :</div> <?php echo $currentMonth['value'] ?> le <?php echo $currentMonth['date'] ?></li>
            </ul>
        </div>
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
                    <?php for($y = 2020 ; $y <= date('Y') ; $y++): ?>
                        <div class="table-cell text-center"><?php echo $y ?></div>
                    <?php endfor ?>
                </div>
            </div>
            <div class="table-row-group">
                <?php for($dow = 1 ; $dow <= 7 ; $dow++): ?>
                    <div class="table-row text-center <?php echo ($dow%2==0 ? 'bg-gray-200' : '') ?>">
                        <div class="table-cell text-left p-1 font-bold"><?php echo Helper::frenchDayOfTheWeek($dow) ?></div>
                        <?php for($y = 2020 ; $y <= date('Y') ; $y++): ?>
                            <div class="table-cell">
                                <span <?php echo ($days[$y][$dow]['value'] === $maxDay[$dow] ? 'class="font-bold"' : '') ?>>
                                    <?php echo $days[$y][$dow]['value'] ?>
                                </span>
                                <div class="text-sm"><em>(<?php echo $days[$y][$dow]['date'] ?>)</em></div>
                            </div>
                        <?php endfor ?>
                    </div>
                <?php endfor ?>
            </div>
        </div>
        <h3 class="text-2xl text-black">
            Comparaison année en cours
        </h3>
        <div class="flex">
            <div class="w-1/2 pr-3">
                <div class="font-bold">Toutes les données</div>
                <?php $tmpDays = $compteur->get('days'); ?>
                <?php require(dirname(__FILE__).'/parts/table-day.php') ?>
            </div>
            <div class="w-1/2 pr-3">
                <div class="font-bold">En <?php echo date('Y') ?></div>
                <?php $tmpDays = $days[date('Y')] ?>
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
        <div class="flex pb-5">
            <div class="w-1/2">
                <b>Toutes les données</b>
                <div>
                    <canvas id="pie-day-<?php echo uniqid() ?>" class="pie pie-days" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values($compteur->getWeekWeekend())) ?>'></canvas>
                </div>
            </div>
            <div class="w-1/2">
                <b>En <?php echo date('Y') ?></b>
                <div>
                    <canvas id="pie-day2-<?php echo uniqid() ?>" class="pie pie-days2" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values($compteur->getWeekWeekend(date('Y')))) ?>'></canvas>
                </div>
            </div>
        </div>
        <h3 class="text-2xl text-black">
            Jours ouvrés vs jours chomés, week-end, fériés en <?php echo date('Y') ?>
        </h3>
        <p><em>Sont considérés comme jours ouvrés les lundi, mardi, mercredi, jeudi, vendredi, hors jours fériés.</em></p>
        <div class="flex pb-5">
            <?php list($jo, $jc) = $compteur->getDayByType() ?>
            <div class="w-1/2">
                <b>Jours ouvrés</b>
                <div>
                    <canvas id="line-jo-<?php echo uniqid() ?>" class="line" data-labels='<?php echo json_encode(array_column($jo, 'date')) ?>' data-values='<?php echo json_encode(array_column($jo, 'value')) ?>' data-max="<?php echo max(max(array_column($jo, 'value')), max(array_column($jc, 'value'))) * 1.1 ?>"></canvas>
                </div>
            </div>
            <div class="w-1/2">
                <b>Jours chomés, week-end, fériés</b>
                <div>
                    <canvas id="line-jc-<?php echo uniqid() ?>" class="line" data-labels='<?php echo json_encode(array_column($jc, 'date')) ?>' data-values='<?php echo json_encode(array_column($jc, 'value')) ?>' data-max="<?php echo max(max(array_column($jo, 'value')), max(array_column($jc, 'value'))) * 1.1 ?>"></canvas>
                </div>
            </div>
        </div>
        <h3 class="text-2xl text-black">
            Autre
        </h3>
        <div class="flex pb-5">
            <div class="w-1/2">
                <b>Données brutes</b>
                <em>(Scroll au sein du tableau)</em>
                <div class="h-80 overflow-y-scroll">
                    <?php $max = 0 ?>
                    <?php $maxY = 0 ?>
                    <ul class="list-group">
                        <?php foreach($data as $date => $val): ?>
                            <?php
                                if($val > $max){
                                    $class = 'bg-green-300';
                                    $max = $val;
                                }elseif(substr($date, -4) == _YEAR_ && $val > $maxY){
                                    $class = 'bg-blue-300';
                                    $maxY = $val;
                                }else{
                                    $class = '';
                                }
                            ?>
                            <li class="flex border hover:bg-gray-200 border-dotted <?php echo $class ?> <?php echo ($compteur->get('recordTotal') == $val ? 'bg-green-500' : '') ?> <?php echo ($compteur->get('recordYear') == $val ? 'bg-blue-500' : '') ?>">
                                <div class="font-bold w-1/3"><?php echo $date ?> : </div><div class="pl-1"><?php echo $val ?></div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
            <div class="w-1/2">
                <b>Valeurs incrémentales</b>
                <div>
                    <canvas id="stack-<?php echo uniqid() ?>" class="bar-stack" data-labels='<?php echo json_encode(array_keys($stack)) ?>' data-values='<?php echo json_encode(array_values($stack)) ?>' data-max="<?php echo max($stack) ?>"></canvas>
                </div>
            </div>
        </div>
        <div class="flex">
            <div class="w-1/2">
                <ul class="list-unstyled">
                    <li><button class="rounded-xl w-5 bg-green-500">&nbsp;</button> Record absolu</li>
                    <li><button class="rounded-xl w-5 bg-green-300">&nbsp;</button> Record absolu jusque là</li>
                    <li><button class="rounded-xl w-5 bg-blue-500">&nbsp;</button> Record en <?php echo _YEAR_ ?></li>
                    <li><button class="rounded-xl w-5 bg-blue-300">&nbsp;</button> Record en <?php echo _YEAR_ ?> jusque là</li>
                </ul>
            </div>
            <div class="w-1/2">
            </div>
        </div>
    </section>
</main>
<?php require_once(dirname(__FILE__).'/parts/footer.php') ?>
