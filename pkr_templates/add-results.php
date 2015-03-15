<?php

$pkr_result = new Pkr_Result($event_id);
if (isset($_SESSION['pkr_game_'.$event_id])) {
	$pkr_result = $_SESSION['pkr_game_'.$event_id];
}

// echo "<pre>";
// echo "add-results.php";
// echo "<h2>SESSION[pkr_game_".$event_id."]</h2>";
// print_r($_SESSION['pkr_game_'.$event_id]);
// echo "<h2>OBJECT</h2>";
// print_r($pkr_result);
// echo "</pre>";

$event = em_get_event($event_id);
// echo "<pre style='font-size:x-small;'>";
// echo "ADD_RESULTS.PHP";
// echo "EVENT_ID";
// print_r($event_id);
// echo "Pkr_Result";
// print_r($pkr_result);
// echo "<h1>SESSION</h1>";
// print_r($_SESSION);
// echo "</pre>";
?>

<?php
	$pkr_result->route_request();
?>