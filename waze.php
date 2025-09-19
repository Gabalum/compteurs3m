<?php

// Définir les coordonnées du cadre géographique (bbox) : gauche, bas, droite, haut
$left   = 2.2800;   // longitude ouest
$bottom = 48.8500;  // latitude sud
$right  = 2.3000;   // longitude est
$top    = 48.8600;  // latitude nord

// URL de l'API non officielle
$url = "https://www.waze.com/row-rtserver/web/TGeoRSS?bottom={$bottom}&left={$left}&top={$top}&right={$right}&types=alerts,traffic";

// Configuration du contexte HTTP (User-Agent sinon refus possible)
$options = [
    'http' => [
        'header' => "User-Agent: PHP-Waze-Client\r\n"
    ]
];
$context = stream_context_create($options);

// Récupération des données
$response = file_get_contents($url, false, $context);
if ($response === false) {
    die("Erreur lors de la récupération des données Waze.");
}

// Décodage JSON
$data = json_decode($response, true);

// Traitement des alertes
echo "📢 Alertes détectées :\n";
if (!empty($data['alerts'])) {
    foreach ($data['alerts'] as $alert) {
        echo "- Type : " . $alert['type'] . "\n";
        echo "  Description : " . ($alert['description'] ?? 'N/A') . "\n";
        echo "  Position : " . $alert['location']['y'] . ", " . $alert['location']['x'] . "\n\n";
    }
} else {
    echo "Aucune alerte détectée.\n\n";
}

// Traitement des bouchons
echo "🚗 Embouteillages détectés :\n";
if (!empty($data['jams'])) {
    foreach ($data['jams'] as $jam) {
        echo "- Longueur : " . round($jam['length'] / 1000, 2) . " km\n";
        echo "  Délai : " . round($jam['delay'] / 60, 1) . " min\n";
        echo "  Vitesse : " . round($jam['speed'], 1) . " km/h\n\n";
    }
} else {
    echo "Aucun bouchon détecté.\n";
}

