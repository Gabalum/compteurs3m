<div class="bg-white shadow-lg rounded-sm border border-gray-200 mt-5">
    <header class="px-5 py-4 border-b border-gray-100">
        <h4 class="font-semibold text-gray-800"><?php echo $rankingTitle ?></h4>
    </header>
    <ul class="my-1">
        <?php $i = 1; ?>
        <?php foreach($ranking as $date => $value): ?>
            <?php
                $medal = 'bg-white-600 text-black text-sm';
                if($i === 1){
                    $medal = 'bg-yellow-500 text-gray-900 text-base';
                }elseif($i === 2){
                    $medal = 'bg-gray-400 text-neutral-900 text-base';
                }elseif($i === 3){
                    $medal = 'bg-yellow-900 text-white text-base';
                }
            ?>
            <li class="flex px-2 hover:bg-gray-200 <?php echo ($compteur->get('lastValue') == $value ? 'bg-green-100' : '') ?>">
                <div class="w-6 h-6 rounded-full shrink-0  my-2 mr-3 text-center <?php echo $medal ?>">
                    <?php echo $i ?>
                </div>
                <div class="grow flex items-center border-b border-gray-100 text-sm py-1">
                    <div class="grow flex justify-between">
                        <div class="self-center <?php echo ($i < 4 ? 'text-lg' : '') ?> <?php echo ($compteur->get('lastValue') == $value ? 'font-bold' : 'font-normal') ?>">
                            <?php echo $value ?>
                        </div>
                        <div class="shrink-0 self-start ml-2 text-gray-400 text-sm">
                            <?php echo $date ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php $i++ ?>
        <?php endforeach ?>
    </ul>
</div>
