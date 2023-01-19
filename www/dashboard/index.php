<?php
    namespace App;
    require_once(dirname(__DIR__, 2).'/bootstrap.php');
    $tomtom = (Tomtom::getInstance())->getData();
    $title = 'Dashboard Compteurs 3M';
    require_once(dirname(__FILE__).'/parts/header.php');
    $yesterday = (new \DateTime())->modify('-1 day')->format('d-m-Y');
    $records = [];
    $tsRecords = [];
    foreach($compteurs as $compteur){
        if($compteur->get('lastDate') !== $yesterday){
            continue;
        }
        $monthes = $compteur->get('monthes');
        $currentMonth = (is_array($monthes) && isset($monthes[date('m')]) ? $monthes[date('m')] : null);
        if($compteur->get('lastValue') === $compteur->get('recordTotal')){
            $records[] = [
                'class'  => 'a text-green-600',
                'text'   => 'Nouveau record absolu pour <b>'.$compteur->get('labelHTML').'</b> ('.$compteur->get('lastValue').')',
            ];
        } elseif($compteur->get('lastValue') === $compteur->get('recordYear')){
            $records[] = [
                'class'  => 'b text-blue-600',
                'text'   => 'Nouveau record de l\'année pour <b>'.$compteur->get('labelHTML').'</b> ('.$compteur->get('lastValue').')',
            ];
        } elseif(!is_null($currentMonth) && $compteur->get('lastValue') === $currentMonth['value']){
            $records[] = [
                'class'  => 'c text-amber-600',
                'text'   => 'Nouveau record du mois pour <b>'.$compteur->get('labelHTML').'</b> ('.$compteur->get('lastValue').')',
            ];
        } elseif($compteur->get('lastValue') === $compteur->get('worstTotal')){
            $records[] = [
                'class'  => 'd text-orange-900',
                'text'   => 'Pire score absolu pour <b>'.$compteur->get('labelHTML').'</b> ('.$compteur->get('lastValue').')',
            ];
        } elseif($compteur->get('lastValue') === $compteur->get('worstYear')){
            $records[] = [
                'class'  => 'd text-orange-900',
                'text'   => 'Pire score de l\'année pour <b>'.$compteur->get('labelHTML').'</b> ('.$compteur->get('lastValue').')',
            ];
        }

        $ts = (new Timeserie($compteur->get('id')))->getData();
        if(is_array($ts) && isset($ts['record'])){
            $tsRecords[] = [$compteur->get('labelHTML'), $ts['record']['value'], $ts['record']['date']];
        }

    }
    uasort($records, function($a, $b){
        return $a['class'] > $b['class'];
    });


?>
<main class="w-full flex-grow p-6 pb-20">
    <h1 class="text-3xl text-black pb-6 text-center">Dashboard des compteurs vélo 3M</h1>
    <?php if(count($records) > 0): ?>
        <section id="facts">
            <h2 class="text-2xl text-black">Faits marquants</h2>
            <div class="bg-white text-sm outline-dashed outline-2 outline-offset-2 border-rose-500 outline-rose-500 shadow-sm rounded-4 py-2 px-2 my-2">
                <ul>
                    <?php foreach($records as $record): ?>
                        <li class="<?php echo $record['class'] ?>">
                            <?php echo $record['text'] ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </section>
    <?php endif ?>
    <section id="derniers" class="pt-10">
        <h2 class="text-2xl text-black">Derniers relevés</h2>
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/4" rowspan="2">Compteur</th>
                    <th rowspan="2">Dernier relevé</th>
                    <th colspan="2">Année <?php echo date('Y') ?></th>
                    <th colspan="2">Total</th>
                </tr>
                <tr>
                    <th>Record du mois</th>
                    <th>Record de l'année</th>
                    <th>Record pour <?php echo Helper::frenchMonth(date('m'), false) ?></th>
                    <th>Record de l'année</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php $foo = true ?>
                <?php foreach($compteurs as $i => $cpt): ?>
                    <?php
                        $foo = !$foo;
                        $monthes = $cpt->get('monthes');
                        $currentMonth = (is_array($monthes) && isset($monthes[date('m')]) ? $monthes[date('m')] : null);
                        $monthesRecord = $cpt->get('monthesRecord');
                        $mr = (is_array($monthesRecord) && isset($monthesRecord[date('m')]) ? $monthesRecord[date('m')] : ['value' => '-', 'date' => '-']);
                    ?>
                    <tr <?php echo ($foo ? 'class="bg-gray-200"' : '') ?>>
                        <th class="bg-gray-800 text-white text-left pl-2"><?php echo $cpt->get('labelHTML') ?></th>
                        <td class="text-center">
                            <span <?php echo (($cpt->get('lastValue') == $currentMonth['value']||$cpt->get('lastValue') == $mr['value']) ? 'class="font-bold"' : '') ?>>
                                <?php echo $cpt->get('lastValue') ?>
                            </span>
                            <div class="text-sm <?php echo ($yesterday !== $cpt->get('lastDate') ? 'text-red-500' : 'text-gray-400') ?>">
                                (<?php echo $cpt->get('lastDate') ?>)
                            </div>
                        </td>
                        <td class="text-center">
                            <span <?php echo ($currentMonth['value'] == $mr['value'] ? 'class="font-bold"' : '') ?>>
                                <?php echo $currentMonth['value'] ?>
                            </span>
                            <div class="text-sm text-gray-400">(<?php echo $currentMonth['date'] ?>)</div>
                        </td>
                        <td class="text-center">
                            <span <?php echo ($cpt->get('recordYear') == $cpt->get('recordTotal') ? 'class="font-bold"' : '') ?>>
                                <?php echo $cpt->get('recordYear') ?>
                            </span>
                            <div class="text-sm text-gray-400">(<?php echo $cpt->get('recordYearDate') ?>)</div>
                        </td>
                        <td class="text-center <?php echo ($foo ? 'bg-gray-300' : 'bg-gray-200') ?>">
                            <span <?php echo ($currentMonth['value'] < $mr['value'] ? 'class="font-bold"' : '') ?>>
                                <?php echo $mr['value'] ?>
                            </span>
                            <div class="text-sm text-gray-400">(<?php echo $mr['date'] ?>)</div>
                        </td>
                        <td class="text-center <?php echo ($foo ? 'bg-gray-300' : 'bg-gray-200') ?>">
                            <span <?php echo ($cpt->get('recordYear') < $cpt->get('recordTotal') ? 'class="font-bold"' : '') ?>>
                                <?php echo $cpt->get('recordTotal') ?>
                            </span>
                            <div class="text-sm text-gray-400">(<?php echo $cpt->get('recordTotalDate') ?>)</div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </section>
    <section id="totaux" class="pt-10">
        <h2 class="text-2xl text-black">Totaux par années</h2>
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="w-1/4">Compteur</th>
                    <?php for($i = 2020 ; $i <= _YEAR_ ; $i++): ?>
                        <th><?php echo $i ?></th>
                    <?php endfor ?>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php $foo = true ?>
                <?php foreach($compteurs as $i => $cpt): ?>
                    <?php
                        $foo = !$foo;
                    ?>
                    <tr <?php echo ($foo ? 'class="bg-gray-200"' : '') ?>>
                        <th class="bg-gray-800 text-white text-left pl-2"><?php echo $cpt->get('labelHTML') ?></th>
                        <?php for($i = 2020 ; $i <= _YEAR_ ; $i++): ?>
                            <td class="text-right pr-3 <?php if($i === _YEAR_): ?><?php echo ($foo ? 'bg-gray-300' : 'bg-gray-200') ?><?php endif ?>">
                                <?php echo Helper::nf($cpt->get('sumByYear')[$i]) ?>
                            </td>
                        <?php endfor ?>
                        <th class="bg-gray-800 text-white text-right pr-3"><?php echo Helper::nf($cpt->get('sumTotal')) ?></th>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </section>

    <?php if(count($tsRecords) > 0): ?>
        <section id="recordsTs" class="pt-10">
            <h2 class="text-2xl text-black">Records horaires de l'année</h2>
            <table class="min-w-full bg-white">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="w-1/4">Compteur</th>
                        <th colspan="2">Valeur</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php foreach($tsRecords as $i => $ts): ?>
                        <tr <?php echo ($i%2 == 0 ? 'class="bg-gray-200"' : '') ?>>
                            <th class="text-right"><?php echo $ts[0] ?></th>
                            <td class="text-center"><b><?php echo $ts[1] ?></b></td>
                            <td><?php echo $ts[2] ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </section>
    <?php endif ?>

    <section id="vs" class="pt-10">
        <h2 class="text-2xl text-black">Semaine vs week-end</h2>
        <div class="grid grid-col-2 md:grid-flow-col gap-4 pb-5 auto-cols-fr">
            <div class="w-full h-fit flex-0 bg-white shadow-lg rounded-sm border border-gray-200 mt-5">
                <header class="px-5 py-4 border-b border-gray-100">
                    <h4 class="font-semibold text-gray-800">Toutes les données</h4>
                </header>
                <div class="pb-5">
                    <canvas id="pie-day-general" class="pie pie-days" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values((Compteurs::getInstance())->getWeekWeekend())) ?>'></canvas>
                </div>
            </div>
            <div class="w-full h-fit flex-0 bg-white shadow-lg rounded-sm border border-gray-200 mt-5">
                <header class="px-5 py-4 border-b border-gray-100">
                    <h4 class="font-semibold text-gray-800">En <?php echo _YEAR_ ?></h4>
                </header>
                <div class="pb-5">
                    <canvas id="pie-day2-general" class="pie pie-days2" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values((Compteurs::getInstance())->getWeekWeekend(date('Y')))) ?>'></canvas>
                </div>
            </div>
        </div>
    </section>
    <?php if(is_array($tomtom) && count($tomtom) > 0): ?>
        <section id="tomtom" class="pt-10">
            <h2 class="text-2xl text-black">Congestion automobile</h2>
            <p><em>Données Tomtom, pourcentage de <a href="https://www.tomtom.com/blog/road-traffic/urban-traffic-congestion/" target="_blank">congestion automobile</a> à l'échelle de la ville</em></p>
            <div class="row" id="tomtom">
                <canvas id="tomtom-day" class="bar-tomtom" data-labels='<?php echo json_encode(array_column($tomtom, 'date')) ?>' data-values='<?php echo json_encode(array_column($tomtom, 'congestion')) ?>'></canvas>
            </div>
        </section>
    <?php endif ?>
</main>
<footer class=" w-full p-4 bg-blue-600 text-white">
    Téléchargements (format CSV) :
    <a href="./raw-dl.php" download class="px-2 py-2 rounded bg-blue-400 hover:bg-blue-900 text-slate-100">Données compteurs</a>
    <a href="./meteo-dl.php" download class="px-2 py-2 rounded bg-blue-400 hover:bg-blue-900 text-slate-100">Données météo</a>
</footer>
<?php require_once(dirname(__FILE__).'/parts/footer.php') ?>
