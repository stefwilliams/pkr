<?php
// register Series post type


add_action( 'init', 'register_cpt_pkr_series' );

function register_cpt_pkr_series() {
    $labels = array(
        'name' => _x( 'Series', 'pkr_series' ),
        'singular_name' => _x( 'Series', 'pkr_series' ),
        'add_new' => _x( 'Add New', 'pkr_series' ),
        'add_new_item' => _x( 'Add New Series', 'pkr_series' ),
        'edit_item' => _x( 'Edit Series', 'pkr_series' ),
        'new_item' => _x( 'New Series', 'pkr_series' ),
        'view_item' => _x( 'View Series', 'pkr_series' ),
        'search_items' => _x( 'Search Series', 'pkr_series' ),
        'not_found' => _x( 'No Series found', 'pkr_series' ),
        'not_found_in_trash' => _x( 'No Series found in Trash', 'pkr_series' ),
        'parent_item_colon' => _x( 'Parent Series:', 'pkr_series' ),
        'menu_name' => _x( 'Series', 'pkr_series' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Custom Post type for Series',
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

register_post_type( 'pkr_series', $args );

}
?>