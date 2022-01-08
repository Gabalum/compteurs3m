<?php
$velocistes = [
    'Le Vieu-Biclou, 5 rue de la Poésie, 34000 Montpellier',
    'Made in Cycle, 600 av. de l\'Aube Rouge, 34170 Castelnau-le-Lez',
    'Au bon vélo, 35 rue Saint-Guilhem, 34000 Montpellier',
    'W Ville & Vélo, 41 Bd de Strasbourg, 34000 Montpellier',
    'Cyclable centre, 7bis quai des Tanneurs, 34000 Montpellier',
    'Cyclable sud, 290 av. Théroigne de Méricourt, 34000 Montpellier',
    'Uni re-cycle, 7 Rue Raoux, 34000 Montpellier',
    "M'Vélo, 465 av. du Pont-Trinquat, 34000 Montpellier",
    "Comebike, 2 rue Jacques Draparnaud, 34000 Montpellier",
    "Cycles et re-cycle, 6 av. Bouisson Bertrand, 34000 Montpellier",
];
shuffle($velocistes);
$message = "Un problème ? Je suis désolé pour toi !\nVoici un réparateur qui pourra peut-être t'aider :\n";
$message .= $velocistes[0];
header('Content-type: application/json');
echo json_encode([
	'response_type' => 'in_channel',
	'text'			=> $message,
]);
