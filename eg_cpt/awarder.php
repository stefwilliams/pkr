<?php
// register Awarding Body post type


add_action( 'init', 'register_cpt_lmw_awarder' );

function register_cpt_lmw_awarder() {
    $labels = array(
        'name' => _x( 'Awarding Bodies', 'lmw_awarder' ),
        'singular_name' => _x( 'Awarding Body', 'lmw_awarder' ),
        'add_new' => _x( 'Add New', 'lmw_awarder' ),
        'add_new_item' => _x( 'Add New Awarding Body', 'lmw_awarder' ),
        'edit_item' => _x( 'Edit Awarding Body', 'lmw_awarder' ),
        'new_item' => _x( 'New Awarding Body', 'lmw_awarder' ),
        'view_item' => _x( 'View Awarding Body', 'lmw_awarder' ),
        'search_items' => _x( 'Search Awarding Bodies', 'lmw_awarder' ),
        'not_found' => _x( 'No Awarding Bodies found', 'lmw_awarder' ),
        'not_found_in_trash' => _x( 'No Awarding Bodies found in Trash', 'lmw_awarder' ),
        'parent_item_colon' => _x( 'Parent Awarding Body:', 'lmw_awarder' ),
        'menu_name' => _x( 'Awarding Bodies', 'lmw_awarder' ),
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Custom Post type for Awarding Bodies',
        'supports' => array( 'title', 'author', 'thumbnail', 'revisions' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=pkr_game',
        'menu_position' => 15,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug'=>'awarding-bodies','with_front'=>false),
        'capability_type' => 'post'
    );

register_post_type( 'lmw_awarder', $args );

}
?>