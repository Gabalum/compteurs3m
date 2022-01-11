<aside class="relative bg-slate-900 text-white h-screen flex flex-col w-50  shadow-xl  overflow-y-scroll">
    <div class="p-2 pb-2 text-center ">
       <a href="./" class="text-white text-xl font-semibold uppercase hover:text-gray-300">Compteurs 3M</a>
   </div>
   <nav class="text-white text-base  flex-grow font-semibold pt-1 pb-20">
       <?php foreach($compteurs as $cpt): ?>
           <a href="./<?php echo $cpt->get('slug') ?>" class="flex items-center <?php echo ($cpt->get('slug') == $slug ? 'active-nav-link bg-blue-900' : '') ?> hover:bg-blue-600 text-white py-1 pl-6 nav-item">
               <?php echo $cpt->get('labelHTML') ?>
           </a>
       <?php endforeach ?>
   </nav>
   <div class="sticky bottom-0 bg-gray-800 p-2">
       <div class="text-center">
           <a href="./" class="pr-1"><i class="fas fa-home"></i></a>
           <a href="./moyennes-mobiles" class="pr-1"><i class="fas fa-chart-area"></i></a>
           <a href="./carte" class="pr-1"><i class="fas fa-map-marked-alt"></i></a>
           <a href="./series-temporelles" class="pr-1"><i class="fas fa-chart-line"></i></a>
       </div>
   </div>
</aside>
