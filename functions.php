<?php
require_once('plugins/bp_rename.php' );
require_once('plugins/bp_group_pkr_add_result.php' );
require_once('plugins/bp_group_em_add_event.php' );

require_once('cpt/pkr_result.php' );
require_once('cpt/pkr_ruleset.php' );

require_once('classes/pkr_player.php' );
require_once('classes/pkr_game.php' );
require_once('classes/pkr_result.php' );
// require_once('cpt/pkr_venue.php' );
// require_once('cpt/pkr_league.php' );
// require_once('cpt/pkr_series.php' );

require_once('widgets/pkr_widget_meta.php' );

// require_once('cpt/pkr_taxonomy.php' );

//enqueue twentyfifteen styles

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

add_action('init', 'pkr_start_session', 1);
function pkr_start_session() {
    if(!session_id()) {
    	session_start();
    }
}

add_action('wp_logout', 'pkr_end_session');
add_action('wp_login', 'pkr_end_session');
function pkr_end_session() {
    session_destroy ();
}

// function echo_stuff($input, $input2) {
// 	echo "<pre>";
// 		echo "<h1>FILTER!</h1>";	
// 	var_dump($input);
// 	print_r($input2);
// 	echo "</pre>";
// }

// add_filter( 'em_tickets_bookings_add', 'echo_stuff', 10, 2 );
?>