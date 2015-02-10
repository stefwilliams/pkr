<?php
// register Result post type - This is the main content type under which the rest are nested.

add_action( 'init', 'register_cpt_pkr_result' );

function register_cpt_pkr_result() {

    $labels = array(
        'name' => _x( 'Results', 'pkr_result' ),
        'singular_name' => _x( 'Result', 'pkr_result' ),
        'add_new' => _x( 'Add New Game', 'pkr_result' ),
        'add_new_item' => _x( 'Add New Result', 'pkr_result' ),
        'edit_item' => _x( 'Edit Result', 'pkr_result' ),
        'new_item' => _x( 'New Result', 'pkr_result' ),
        'view_item' => _x( 'View Result', 'pkr_result' ),
        'search_items' => _x( 'Search Results', 'pkr_result' ),
        'not_found' => _x( 'No Results found', 'pkr_result' ),
        'not_found_in_trash' => _x( 'No Results found in Trash', 'pkr_result' ),
        'parent_item_colon' => _x( 'Parent Result:', 'pkr_result' ),
        'menu_name' => _x( 'Poker Stats', 'pkr_result' ),
        'all_items' => _x( 'Results', 'pkr_result')
    );

    $args = array(
        'labels' => $labels,
        'menu_icon' => 'dashicons-chart-line',
        'hierarchical' => false,
        'description' => 'Custom Post type for Results',
        'supports' => array( 'title', 'editor', 'author', 'excerpt', 'comments', 'revisions' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 2,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug'=>'game-results','with_front'=>false),
        'capability_type' => 'post'
    );

register_post_type( 'pkr_result', $args );

}

?>