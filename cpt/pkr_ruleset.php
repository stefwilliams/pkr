<?php

// register Ruleset post type

add_action( 'init', 'register_cpt_pkr_ruleset' );



function register_cpt_pkr_ruleset() {

    $labels = array(

        'name' => _x( 'Ruleset', 'pkr_ruleset' ),

        'singular_name' => _x( 'Ruleset', 'pkr_ruleset' ),

        'add_new' => _x( 'Add New', 'pkr_ruleset' ),

        'add_new_item' => _x( 'Add New Ruleset', 'pkr_ruleset' ),

        'edit_item' => _x( 'Edit Ruleset', 'pkr_ruleset' ),

        'new_item' => _x( 'New Ruleset', 'pkr_ruleset' ),

        'view_item' => _x( 'View Ruleset', 'pkr_ruleset' ),

        'search_items' => _x( 'Search Ruleset', 'pkr_ruleset' ),

        'not_found' => _x( 'No Ruleset found', 'pkr_ruleset' ),

        'not_found_in_trash' => _x( 'No Ruleset found in Trash', 'pkr_ruleset' ),

        'parent_item_colon' => _x( 'Parent Ruleset:', 'pkr_ruleset' ),

        'menu_name' => _x( 'Rulesets', 'pkr_ruleset' ),

    );



    $args = array(

        'labels' => $labels,

        'hierarchical' => false,

        'description' => 'Custom Post type for Ruleset',

        'supports' => array( 'title', 'author', 'thumbnail', 'revisions' ),

        'public' => true,

        'show_ui' => true,

        'show_in_menu' => 'edit.php?post_type=pkr_result',

        'show_in_nav_menus' => true,

        'publicly_queryable' => true,

        'exclude_from_search' => false,

        'has_archive' => true,

        'query_var' => true,

        'can_export' => true,

        'rewrite' => array('slug'=>'pkr-locations','with_front'=>false),

        'capability_type' => 'post'

    );



register_post_type( 'pkr_ruleset', $args );



}

?>