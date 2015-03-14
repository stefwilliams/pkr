<?php
// register Venue post type


add_action( 'init', 'register_cpt_pkr_venue' );

function register_cpt_pkr_venue() {
    $labels = array(
        'name' => _x( 'Venues', 'pkr_venue' ),
        'singular_name' => _x( 'Venue', 'pkr_venue' ),
        'add_new' => _x( 'Add New', 'pkr_venue' ),
        'add_new_item' => _x( 'Add New Venue', 'pkr_venue' ),
        'edit_item' => _x( 'Edit Venue', 'pkr_venue' ),
        'new_item' => _x( 'New Venue', 'pkr_venue' ),
        'view_item' => _x( 'View Venue', 'pkr_venue' ),
        'search_items' => _x( 'Search Venues', 'pkr_venue' ),
        'not_found' => _x( 'No Venues found', 'pkr_venue' ),
        'not_found_in_trash' => _x( 'No Venues found in Trash', 'pkr_venue' ),
        'parent_item_colon' => _x( 'Parent Venue:', 'pkr_venue' ),
        'menu_name' => _x( 'Venues', 'pkr_venue' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Custom Post type for Venues',
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

register_post_type( 'pkr_venue', $args );

}
?>