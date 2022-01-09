<?php namespace App; ?>
<div class="table min-w-full bg-white border border-gray-500">
    <div class="table-header-group bg-slate-900 text-white">
        <div class="table-row flex">
            <div class="table-cell w-1/4 text-center pt-2 pb-2">Jour</div>
            <div class="table-cell w-1/4 text-center pt-2 pb-2">Total</div>
            <div class="table-cell w-1/4 text-center pt-2 pb-2">Moyenne</div>
            <div class="table-cell w-1/4 text-center pt-2 pb-2">Record</div>
        </div>
    </div>
    <div class="table-row-group">
        <?php foreach($tmpDays as $dow => $values): ?>
            <?php
                $css = '';
                if($isCurrentY){
                    if($tmpDays[$dow]['value'] === $maxDay[$dow]){
                        $css .= ' font-bold';
                    }
                    if($tmpDays[$dow]['value'] === $compteur->get('lastValue')){
                        $css .= 'text-green-600';
                    }
                }
            ?>
            <div class="table-row flex text-center <?php echo ($dow%2==0 ? 'bg-gray-200' : '') ?>">
                <div class="table-cell p-1 font-bold text-left">
                    <?php echo Helper::frenchDayOfTheWeek($dow) ?>
                </div>
                <div class="table-cell ">
                    <?php echo $values['sum'] ?>
                    <div class="text-sm"><em>(<?php echo $values['cpt'] ?> jours)</em></div>
                </div>
                <div class="table-cell ">
                    <?php echo $values['avg'] ?>
                    <div class="text-sm">&nbsp;</div>
                </div>
                <div class="table-cell ">
                    <span class="<?php echo $css ?>"><?php echo $values['value'] ?></span>
                    <div class="text-sm"><em>(<?php echo $values['date'] ?>)</em></div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</div>
<div class="mt-2">
    <canvas id="bar-day-<?php echo uniqid() ?>" class="bar bar-days" data-label="par jour " data-labels='<?php echo json_encode(array_map(Helper::class.'::frenchDayOfTheWeek', array_keys($tmpDays))) ?>' data-values='<?php echo json_encode(array_column($tmpDays, 'avg')) ?>' data-global-avg="<?php echo $avg ?>" data-max="<?php echo $max ?>"></canvas>
</div>
