<?php

function sortCmp($a, $b){
    return ($a['valeur'] <= $b['valeur'] ? -1 : 1);
}
$insee = [];
$values = '';
$search = false;
if(isset($_GET['insee'])){
    $dataINSEE = explode(',', $_GET['insee']);
    if(is_array($dataINSEE) && count($dataINSEE) > 0){
        $search = true;
        foreach($dataINSEE as $i){
            $i = trim($i);
            if(substr($i, 0, 1) == 0){
                $i = '0'.(int)$i;
            }else{
                $i = (int) $i;
            }
            if(strlen($i) === 5){
                $insee[] = $i;
            }
        }
        $values = implode(',', $insee);
    }
}
$items = [];
if(count($insee) > 0){
    $date = filemtime(__DIR__.'/barometre2021.json');
    if(((time() - $date) / 60) > 10){
        $handler = fopen(__DIR__.'/barometre2021.json', 'w+');
        $data = file_get_contents('https://barometre.parlons-velo.fr/api/4cds56c4sdc4c56ds4cre84c13ez8c4ezc6eza9c84ze16464cdsc1591cdzf8ez/stats/geojson');
        fwrite($handler, $data);
        fclose($handler);
    }else{
        $data = file_get_contents(__DIR__.'/barometre2021.json');
    }
    $data = @json_decode($data);
    if(is_object($data) && isset($data->features)){
        if(is_array($data->features) && count($data->features) > 0){
            foreach($data->features as $feature){
                if(is_object($feature) && isset($feature->properties) && is_object($feature->properties) && isset($feature->properties->insee)){
                    if(in_array($feature->properties->insee, $insee)){
                        $items[$feature->properties->insee] = [
                            'commune'   => $feature->properties->name,
                            'valeur'    => $feature->properties->contributions,
                        ];
                    }
                }

            }
        }
    }
    usort($items, 'sortCmp');
}
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Recherche sur le baromètre des villes cyclables</title>
    <style>
        .bd-callout-info {
            border-left-color: #5bc0de;
        }
        .bd-callout {
            padding: 1.25rem;
            margin-top: 1.25rem;
            margin-bottom: 1.25rem;
            border: 1px solid #e9ecef;
                border-left-color: rgb(233, 236, 239);
                border-left-width: 1px;
            border-left-width: .25rem;
            border-radius: .25rem;
        }
        h2{
            margin-top:25px;
        }
    </style>
  </head>
 <body>
     <div class="container">
         <h1>Recherche sur le baromètre des villes cyclables</h1>
         <p class="bd-callout bd-callout-info">Recherche par code INSEE uniquement (pas de code postal).<br>
             Vous pouvez chercher plusieurs communes en saisissant plusieurs codes INSEE séparés par une virgule.</p>
             <p class="alert alert-danger">Attention : pour ne pas surcharger le serveur, les données sont mises à jour toutes les 10 minutes</p>
         <form method="get" action="./barometre-recherche.php">
             <fieldset>
                 <div class="row g-3 align-items-center">
                     <div class="col-auto">
                         <label for="insee" class="col-form-label">Code(s) INSEE</label>
                     </div>
                     <div class="col-auto">
                         <input type="text" id="insee" value="<?php echo $values ?>" name="insee" class="form-control" aria-describedby="inseeHelp">
                     </div>
                     <div class="col-auto">
                         <input type="submit" value="Rechercher" class="btn btn-outline-dark">
                      </div>
                  </div>
              </fieldset>
          </form>

          <?php if($search ||count($items) > 0): ?>
              <h2>Résultats</h2>
              <p class="alert alert-warning">Nota bene : si la commune recherchée n'apparaît pas, c'est soit que le code INSEE n'est pas valide, soit qu'il n'y a pas de résultat pour cette commune</p>
              <?php if(count($items) > 0): ?>
                  <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Commune</th>
                            <th>Valeur</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                            <tr <?php if($item['valeur'] >= 50): ?>class="table-success"<?php elseif($item['valeur'] >= 40): ?>class="table-warning"<?php endif ?>>
                                <td><?php echo $item['commune'] ?></td>
                                <td><?php echo $item['valeur'] ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
        <?php endif ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>
