<?php namespace App; ?>
<div class="table min-w-full bg-white border border-gray-500">
    <div class="table-header-group bg-slate-900 text-white">
        <div class="table-row flex">
            <div class="table-cell w-1/4 text-center pt-2 pb-2">N° </div>
            <div class="table-cell w-1/4 text-center pt-2 pb-2">Total</div>
            <div class="table-cell w-1/4 text-center pt-2 pb-2">Moyenne</div>
            <div class="table-cell w-1/4 text-center pt-2 pb-2">Record</div>
        </div>
    </div>
    <div class="table-row-group">
        <?php foreach($weeks as $week => $values): ?>
            <div class="table-row flex text-center <?php echo ($week%2==0 ? 'bg-gray-200' : '') ?>">
                <div class="table-cell p-1 font-bold">
                    <?php echo $week ?>
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
