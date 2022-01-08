<aside class="relative bg-slate-900 text-white h-screen w-50 hidden sm:block shadow-xl  overflow-y-scroll">
    <div class="p-5 pb-2 text-center ">
       <a href="./" class="text-white text-xl font-semibold uppercase hover:text-gray-300">Compteurs 3M</a>
   </div>
   <nav class="text-white text-base font-semibold pt-1">
       <?php foreach($compteurs as $cpt): ?>
           <a href="./detail.php?cpt=<?php echo $cpt->get('slug') ?>" class="flex items-center <?php echo ($cpt->get('slug') == $slug ? 'active-nav-link bg-blue-900' : '') ?> hover:bg-blue-600 text-white py-1 pl-6 nav-item">
               <?php echo $cpt->get('labelHTML') ?>
           </a>
       <?php endforeach ?>
   </nav>
</aside>
