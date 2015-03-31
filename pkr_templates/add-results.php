<?php

$pkr_game = new Pkr_Game($event_id);
if (isset($_SESSION['pkr_game_'.$event_id])) {
	$pkr_game = $_SESSION['pkr_game_'.$event_id];
}

// echo "<pre>";
// echo "add-results.php";
// echo "<h2>SESSION[pkr_game_".$event_id."]</h2>";
// print_r($_SESSION['pkr_game_'.$event_id]);
// echo "<h2>OBJECT</h2>";
// print_r($pkr_game);
// echo "</pre>";

$event = em_get_event($event_id);
// echo "<pre style='font-size:x-small;'>";
// echo "ADD_RESULTS.PHP";
// echo "EVENT_ID";
// print_r($event_id);
// echo "Pkr_game";
// print_r($pkr_game);
// echo "<h1>SESSION</h1>";
// print_r($_SESSION);
// echo "</pre>";
?>

<?php
	$pkr_game->route_request();
?>