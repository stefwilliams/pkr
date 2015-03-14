<?php
// register Provider post type

add_action( 'init', 'register_cpt_lmw_provider' );

function register_cpt_lmw_provider() {

    $labels = array(
        'name' => _x( 'Training Providers', 'lmw_provider' ),
        'singular_name' => _x( 'Provider', 'lmw_provider' ),
        'add_new' => _x( 'Add New', 'lmw_provider' ),
        'add_new_item' => _x( 'Add New Provider', 'lmw_provider' ),
        'edit_item' => _x( 'Edit Provider', 'lmw_provider' ),
        'new_item' => _x( 'New Provider', 'lmw_provider' ),
        'view_item' => _x( 'View Provider', 'lmw_provider' ),
        'search_items' => _x( 'Search Providers', 'lmw_provider' ),
        'not_found' => _x( 'No Providers found', 'lmw_provider' ),
        'not_found_in_trash' => _x( 'No Providers found in Trash', 'lmw_provider' ),
        'parent_item_colon' => _x( 'Parent Provider:', 'lmw_provider' ),
        'menu_name' => _x( 'Providers', 'lmw_provider' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Custom Post type for Providers',
        'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'revisions' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=lmw_course',
        'menu_position' => 15,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug'=>'','with_front'=>false),
        'capability_type' => 'post'
    );

register_post_type( 'lmw_provider', $args );

}
?>