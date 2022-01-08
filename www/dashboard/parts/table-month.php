<?php namespace App; ?>
<div class="table min-w-full bg-white border border-gray-500">
    <div class="table-header-group bg-slate-900 text-white">
        <div class="table-row flex">
            <div class="table-cell w-1/4 text-center pt-2 pb-2">Mois</div>
            <div class="table-cell w-1/4 text-center pt-2 pb-2">Total</div>
            <div class="table-cell w-1/4 text-center pt-2 pb-2">Moyenne</div>
            <div class="table-cell w-1/4 text-center pt-2 pb-2">Record</div>
        </div>
    </div>
    <div class="table-row-group">
        <?php foreach($monthes as $month => $values): ?>
            <div class="table-row flex text-center <?php echo ($month%2==0 ? 'bg-gray-200' : '') ?>">
                <div class="table-cell p-1 font-bold">
                    <?php echo Helper::frenchMonth($month, false) ?>
                </div>
                <?php if($values['cpt'] == 0): ?>
                    <div class="table-cel"></div>
                    <div class="table-cell">
                        -
                        <div class="text-sm">&nbsp;</div>
                    </div>
                    <div class="table-cell"></div>
                <?php else: ?>
                    <div class="table-cell ">
                        <?php echo $values['sum'] ?>
                        <div class="text-sm"><em>(<?php echo $values['cpt'] ?> jours)</em></div>
                    </div>
                    <div class="table-cell ">
                        <?php echo $values['avg'] ?>
                        <div class="text-sm">&nbsp;</div>
                    </div>
                    <div class="table-cell ">
                        <?php echo $values['value'] ?>
                        <div class="text-sm"><em>(<?php echo $values['date'] ?>)</em></div>
                    </div>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>
</div>
<div class="font-bold pt-2">En <?php echo $year ?></div>
<div>
    <canvas id="bar-month-<?php echo uniqid() ?>" class="bar bar-monthes" data-label="par mois " data-labels='<?php echo json_encode(array_map(Helper::class.'::frenchMonthWithoutPrefix', array_keys($monthes))) ?>' data-values='<?php echo json_encode(array_column($monthes, 'avg')) ?>' <?php if($year==date('Y')): ?>data-global-avg="<?php echo $compteur->get('avgCurYear') ?>"<?php endif ?> data-max="<?php echo intval($compteur->get('maxAvgMonthY')*1.1) ?>"></canvas>
</div>
