<?php
    namespace App;
    require_once(dirname(__DIR__, 2).'/bootstrap.php');
    $compteurs = (Compteurs::getInstance())->getCompteurs();
    $rowData = (Compteurs::getInstance())->getAllByDates();
    $rowDataY = (Compteurs::getInstance())->getAllByDates((date('Y') - 1));
    $cptLabels = (Compteurs::getInstance())->getLabels();
    $title = 'Séries temporelles :: Dashboard Compteurs 3M';
    require_once(dirname(__FILE__).'/parts/header.php');
?>
<main class="w-full flex-grow p-6 pb-20 max-h-full">
    <h1 class="text-3xl text-black pb-6 text-center">Séries temporelles</h1>
    <div class="flex" id="map-wrapper">
        <div class="w-1/4" id="legend-linechart">
            <form method="get" action="javascript:void(0);">
                <div class="grid grid-cols-1">
                    <?php foreach($compteurs as $compteur): ?>
                        <label>
                            <input type="checkbox" name="cpt" id="cpt_<?php echo $compteur->get('id') ?>" value="<?php echo $compteur->get('id') ?>" checked>
                            <span class="inline-block w-3 h-3 mr-1 rounded-full" style="background:<?php echo $compteur->get('color') ?>"></span>
                            <?php echo $compteur->get('labelHTML') ?>
                        </label>
                    <?php endforeach ?>
                </div>
            </form>
        </div>
        <div class="w-3/4">
            <canvas id="linechart-general" class="linechart"
                data-label="par semaine"
                data-labels='<?php echo json_encode(array_values($rowData['dates'])) ?>'
                data-labels-previous='<?php echo json_encode(array_values($rowDataY['dates'])) ?>'
                data-values='<?php echo json_encode($rowData['data']) ?>'
                data-values-previous='<?php echo json_encode($rowDataY['data']) ?>'
                data-cpts='<?php echo json_encode($cptLabels) ?>'
            >
            </canvas>
            <div class="text-center mt-3">
                <a id="cpt_show" href="javascript:void(0)" class="mt-2 px-2 py-2 rounded bg-blue-400 hover:bg-blue-900 text-slate-100">Voir tous</a>
                <a id="cpt_hide" href="javascript:void(0)" class="mt-2 px-2 py-2 rounded bg-blue-400 hover:bg-blue-900 text-slate-100">Voir aucun</a>
                <a id="y_previous" href="javascript:void(0)" class="mt-2 px-2 py-2 rounded bg-orange-400 hover:bg-orange-900 text-slate-100">Année <?php echo (date('Y') - 1) ?></a>
                <a id="y_current" href="javascript:void(0)" class="mt-2 px-2 py-2 rounded bg-orange-600 hover:bg-orange-900 text-slate-100">Année <?php echo date('Y') ?></a>
            </div>
        </div>
        </div>

</main>
<?php require_once(dirname(__FILE__).'/parts/footer.php') ?>
