<?php
    namespace App;
    require_once(dirname(__DIR__, 2).'/bootstrap.php');
    $compteurs = (Compteurs::getInstance())->getCompteurs();
    $title = 'La Carte :: Dashboard Compteurs 3M';
    require_once(dirname(__FILE__).'/parts/header.php');
?>
<main class="w-full flex-grow p-6 pb-20 h-screen">
    <h1 class="text-3xl text-black pb-6 text-center">La Carte</h1>
    <div class="flex" id="map-wrapper">
        <div id="legend" class="w-1/4">
            <?php foreach($compteurs as $compteur): ?>
                <div class="flex">
                    <div class="compteur" data-place="<?php echo $compteur->get('slug') ?>" data-lat="<?php echo $compteur->get('lat') ?>" data-lng="<?php echo $compteur->get('lng') ?>" data-color="<?php echo $compteur->get('color') ?>">
                        <span class="inline-block w-3 h-3 mr-1 rounded-full" style="background:<?php echo $compteur->get('color') ?>"></span>
                        <a href="javascript:void(0)" class="text-black-900 hover:text-blue-700" id="<?php echo $compteur->get('slug') ?>">
                            <?php echo $compteur->get('labelHTML') ?>
                        </a>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
        <div class="w-3/4">
            <div id="map-1" data-id="1" class="map w-full h-full" data-center="[43.60815211731254,3.8779338961662457]">&nbsp;</div>
        </div>
    </div>
</main>
<?php $scripts = [
        '<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>',
        '<script src="assets/js/map.js"></script>'
]; ?>
<?php require_once(dirname(__FILE__).'/parts/footer.php') ?>
