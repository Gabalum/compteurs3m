<?php
$today = new DateTime();
$friday = new DateTime();
$friday->modify('last friday of this month'); // dernier vendredi
$difference = $today->diff($friday);
if($difference == 0) {
    $message = "On est vendredi, la Critical Mass c'est ce soir à 19h sur la Comédie";
} else {
    $message = "J-".($difference->days+1)." avant la prochaine Critical Mass (vendredi ".$friday->format('d')."), ça te laisse le temps d'inviter des copains";
}
header('Content-type: application/json');
echo json_encode([
	'response_type' => 'in_channel',
	'text'			=> $message,
]);