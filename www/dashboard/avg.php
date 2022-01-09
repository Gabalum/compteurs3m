<?php
    namespace App;
    require_once(dirname(__DIR__, 2).'/bootstrap.php');
    $compteurs = (Compteurs::getInstance())->getCompteurs();
// ----- calculs des moyennes
    $days = (isset($_GET['days']) ? intval($_GET['days']) : 0);
    $s1 = null;
    if($days < 3 || $days > 8){
        $days = 3;
    }else{
        $s1 = $days;
    }
    $s2 = null;
    $days2 = (isset($_GET['days2']) ? intval($_GET['days2']) : 0);
    if($days2 === 0){
        $s2 = null;
        $days2 = $days;
    }else{
        if($days2 < 3 || $days2 > 8){
            $days2 = 3;
        }else{
            $s2 = $days2;
        }
    }
    $avg = [];
    $avg2 = [];
    $s3 = (isset($_GET['cpt']) ? strip_tags($_GET['cpt']) : 'all');
    foreach($compteurs as $k => $cpt){
        if($s3 !== 'all' && $cpt->get('slug') !== $s3){
            continue;
        }
        $data = $cpt->get('dataCurYear');
        $i = 0;
        $avg[$k] = [];
        $avg2[$k] = [];
        if(is_array($data) && count($data) > 0){
            foreach($data as $ts => $item){
                $x[$i] = $item['value'];
                if($i > ($days-2)){
                    $sum = 0;
                    for($z = 0 ; $z < $days; $z++){
                        $sum += $x[$i-$z];
                    }
                    $avg[$k][$item['date']] = $sum/$days;
                }
                if($i > ($days2-2)){
                    $sum = 0;
                    for($z = 0 ; $z < $days2; $z++){
                        $sum += $x[$i-$z];
                    }
                    $avg2[$k][$item['date']] = $sum/$days2;
                }
                $i++;
            }
        }
    }
// ----- ! calculs des moyennes

    $title = 'Moyennes mobiles :: Dashboard Compteurs 3M';
    require_once(dirname(__FILE__).'/parts/header.php');
?>
<main class="w-full flex-grow p-6 pb-20">
    <h1 class="text-3xl text-black pb-6 text-center">Moyennes mobiles</h1>
    <div class="bg-slate-200 text-black p-4 pl-10 pr-10 rounded-xl">
        <form method="get">
            <div class="flex flex-col md:flex-row">
                <div class="w-full md:w-1/4">
                    <label for="days" class="">Nombre de jours : </label>
                    <select name="days" id="days" class="md:block form-select px-4 py-3 rounded-full">
                        <option value="3" <?php echo (is_null($s1) ? 'selected' : '') ?>>-- Choisir</option>
                        <?php for($i = 3; $i < 8; $i++): ?>
                            <option value="<?php echo $i ?>" <?php echo ($s1 == $i ? 'selected' : '') ?>><?php echo $i ?> jours</option>
                        <?php endfor ?>
                    </select>
                </div>
                <div class="w-full md:w-1/4">
                    <label for="days2" class="">Nombre de jours (comparatif) : </label>
                    <select name="days2" id="days2" class="md:block form-select px-4 py-3 rounded-full">
                        <option value="0" <?php echo (is_null($s2) ? 'selected' : '') ?>>-- Choisir</option>
                        <?php for($i = 3; $i < 8; $i++): ?>
                            <option value="<?php echo $i ?>" <?php echo ($s2 == $i ? 'selected' : '') ?>><?php echo $i ?> jours</option>
                        <?php endfor ?>
                    </select>
                </div>
                <div class="w-full md:w-1/4">
                    <label for="cpt" class="">Compteurs : </label>
                    <select name="cpt" id="cpt" class="md:block form-select px-4 py-3 rounded-full">
                        <option value="all" <?php echo (is_null($s2) ? 'selected' : '') ?>>-- TOUS</option>
                        <?php foreach($compteurs as $compteur): ?>
                            <option value="<?php echo $compteur->get('slug') ?>" <?php echo ($s3 == $compteur->get('slug') ? 'selected' : '') ?>><?php echo $compteur->get('labelHTML') ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="w-full md:w-1/4">
                    <br class="hidden md:block">
                    <button type="submit" class="bg-blue-800 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Valider</button>
                </div>
            </div>
        </form>
    </div>
    <?php if(count($compteurs) > 0): ?>
        <section id="compteurs">
            <?php foreach($compteurs as $k => $c): ?>
                <?php
                    if($s3 !== 'all' && $c->get('slug') !== $s3){
                        continue;
                    }
                ?>
                <article id="cpt-<?php echo $k ?>" class="mt-5">
                    <h2 class="text-black text-2xl"><?php echo $c->get('labelHTML') ?></h2>
                    <div class="flex flex-col md:flex-row">
                        <?php if($days !== $days2): ?>
                            <div class="w-full md:w-1/2">
                                <b>Lissage sur <?php echo $days ?> jours</b>
                                <canvas id="line-<?php echo $k ?>" class="line" data-labels='<?php echo json_encode(array_keys($avg[$k])) ?>' data-values='<?php echo json_encode(array_values($avg[$k])) ?>' data-max="<?php echo max(array_values($avg[$k])) ?>"></canvas>
                            </div>
                            <div class="w-full md:w-1/2">
                                <b>Lissage sur <?php echo $days2 ?> jours</b>
                                <canvas id="line2-<?php echo $k ?>" class="line line2" data-labels='<?php echo json_encode(array_keys($avg2[$k])) ?>' data-values='<?php echo json_encode(array_values($avg2[$k])) ?>' data-max="<?php echo max(array_values($avg2[$k])) ?>"></canvas>
                            </div>
                        <?php else: ?>
                            <div class="w-full">
                                <b>Lissage sur <?php echo $days ?> jours</b>
                                <canvas id="line-<?php echo $k ?>" class="line" data-labels='<?php echo json_encode(array_keys($avg[$k])) ?>' data-values='<?php echo json_encode(array_values($avg[$k])) ?>' data-max="<?php echo max(array_values($avg[$k])) ?>"></canvas>
                            </div>
                        <?php endif ?>
                    </div>
                </article>
            <?php endforeach ?>
        </section>
    <?php endif ?>
</main>
<?php require_once(dirname(__FILE__).'/parts/footer.php') ?>
