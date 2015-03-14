<?php
// register Leagues post type


add_action( 'init', 'register_cpt_pkr_leagues' );

function register_cpt_pkr_leagues() {
    $labels = array(
        'name' => _x( 'Leagues', 'pkr_leagues' ),
        'singular_name' => _x( 'Leagues', 'pkr_leagues' ),
        'add_new' => _x( 'Add New', 'pkr_leagues' ),
        'add_new_item' => _x( 'Add New Leagues', 'pkr_leagues' ),
        'edit_item' => _x( 'Edit Leagues', 'pkr_leagues' ),
        'new_item' => _x( 'New Leagues', 'pkr_leagues' ),
        'view_item' => _x( 'View Leagues', 'pkr_leagues' ),
        'search_items' => _x( 'Search Leagues', 'pkr_leagues' ),
        'not_found' => _x( 'No Leagues found', 'pkr_leagues' ),
        'not_found_in_trash' => _x( 'No Leagues found in Trash', 'pkr_leagues' ),
        'parent_item_colon' => _x( 'Parent Leagues:', 'pkr_leagues' ),
        'menu_name' => _x( 'Leagues', 'pkr_leagues' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Custom Post type for Leagues',
        'supports' => array( 'title', 'author', 'thumbnail', 'revisions' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=pkr_game',
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug'=>'pkr-locations','with_front'=>false),
        'capability_type' => 'post'
    );

register_post_type( 'pkr_leagues', $args );

}
?>