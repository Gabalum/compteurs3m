<?php
    namespace App;
    require_once(dirname(__DIR__, 2).'/bootstrap.php');
    $tomtom = (Tomtom::getInstance())->getData();
    require_once(dirname(__FILE__).'/parts/header.php');
?>
<main class="w-full flex-grow p-6 pb-20">
    <h1 class="text-3xl text-black pb-6 text-center">Dashboard des compteurs vélo 3M</h1>
    <section id="derniers">
        <h2 class="text-2xl text-black">Derniers relevés</h2>
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th rowspan="2">Compteur</th>
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
                        <th class="bg-gray-800 text-white"><?php echo $cpt->get('labelHTML') ?></th>
                        <td class="text-center">
                            <span <?php echo (($cpt->get('lastValue') == $currentMonth['value']||$cpt->get('lastValue') == $mr['value']) ? 'class="font-bold"' : '') ?>>
                                <?php echo $cpt->get('lastValue') ?>
                            </span>
                            <div class="text-sm"><em>(<?php echo $cpt->get('lastDate') ?>)</em></div>
                        </td>
                        <td class="text-center">
                            <span <?php echo ($currentMonth['value'] == $mr['value'] ? 'class="font-bold"' : '') ?>>
                                <?php echo $currentMonth['value'] ?>
                            </span>
                            <div class="text-sm"><em>(<?php echo $currentMonth['date'] ?>)</em></div>
                        </td>
                        <td class="text-center">
                            <span <?php echo ($cpt->get('recordYear') == $cpt->get('recordTotal') ? 'class="font-bold"' : '') ?>>
                                <?php echo $cpt->get('recordYear') ?>
                            </span>
                            <div class="text-sm"><em>(<?php echo $cpt->get('recordYearDate') ?>)</em></div>
                        </td>
                        <td class="text-center <?php echo ($foo ? 'bg-gray-400' : 'bg-gray-300') ?>">
                            <span <?php echo ($currentMonth['value'] < $mr['value'] ? 'class="font-bold"' : '') ?>>
                                <?php echo $mr['value'] ?>
                            </span>
                            <div class="text-sm"><em>(<?php echo $mr['date'] ?>)</em></div>
                        </td>
                        <td class="text-center <?php echo ($foo ? 'bg-gray-400' : 'bg-gray-300') ?>">
                            <span <?php echo ($cpt->get('recordYear') < $cpt->get('recordTotal') ? 'class="font-bold"' : '') ?>>
                                <?php echo $cpt->get('recordTotal') ?>
                            </span>
                            <div class="text-sm"><em>(<?php echo $cpt->get('recordTotalDate') ?>)</em></div>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </section>
    <section id="vs" class="pt-10">
        <h2 class="text-2xl text-black">Semaine vs week-end</h2>
        <div class="flex">
            <div class="w-1/2">
                <b>Toutes les données</b>
                <div>
                    <canvas id="pie-day-general" class="pie pie-days" data-labels='<?php echo json_encode(['En semaine', 'Le week-end']) ?>' data-values='<?php echo json_encode(array_values((Compteurs::getInstance())->getWeekWeekend())) ?>'></canvas>
                </div>
            </div>
            <div class="w-1/2">
                <b>En <?php echo date('Y') ?></b>
                <div>
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
