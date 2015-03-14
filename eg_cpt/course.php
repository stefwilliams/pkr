<?php
// register Poker Game post type - This is the main content type under which the rest are nested.

add_action( 'init', 'register_cpt_pkr_game' );

function register_cpt_pkr_game() {

    $labels = array(
        'name' => _x( 'Poker Games', 'pkr_game' ),
        'singular_name' => _x( 'Poker Game', 'pkr_game' ),
        'add_new' => _x( 'Add New', 'pkr_game' ),
        'add_new_item' => _x( 'Add New Poker Game', 'pkr_game' ),
        'edit_item' => _x( 'Edit Poker Game', 'pkr_game' ),
        'new_item' => _x( 'New Poker Game', 'pkr_game' ),
        'view_item' => _x( 'View Poker Game', 'pkr_game' ),
        'search_items' => _x( 'Search Poker Games', 'pkr_game' ),
        'not_found' => _x( 'No Poker Games found', 'pkr_game' ),
        'not_found_in_trash' => _x( 'No Poker Games found in Trash', 'pkr_game' ),
        'parent_item_colon' => _x( 'Parent Poker Game:', 'pkr_game' ),
        'menu_name' => _x( 'Training', 'pkr_game' ),
        'all_items' => _x( 'Poker Games', 'pkr_game')
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'description' => 'Custom Post type for Poker Games',
        'supports' => array( 'title', 'editor', 'author', 'revisions' ),
        'taxonomies' => array( 'tc_category' ),
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 15,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array('slug'=>'training-Poker Games','with_front'=>false),
        'capability_type' => 'post'
    );

register_post_type( 'pkr_game', $args );

}

// Define icon styles for the custom post type
function lmw_training_icons() {
    $img_path = plugins_url( 'training' );
    $img_path .= '/icons/training';
    ?>

<style type="text/css" media="screen">

        #menu-posts-pkr_game .wp-menu-image {
            background: url(<?php echo $img_path; ?>.png) no-repeat 6px -17px !important;
        }
        #menu-posts-pkr_game:hover .wp-menu-image {
            background-position:6px 7px !important;
        }
        #menu-posts-pkr_game .wp-menu-open .wp-menu-image {
            background: url(<?php echo $img_path; ?>-selected.png) no-repeat 6px -17px !important;
        }
        #menu-posts-pkr_game:hover .wp-menu-open .wp-menu-image {
            background-position:6px 7px !important;
        }        
        #icon-edit.icon32-posts-pkr_game, #icon-edit.icon32-posts-lmw_provider, #icon-edit.icon32-posts-lmw_awarder {background: url(<?php echo $img_path; ?>-32.png) no-repeat;}
</style>

<?php }
add_action( 'admin_head', 'lmw_training_icons' );


// register Poker Game Category taxonomy

add_action( 'init', 'register_taxonomy_tc_category' );

function register_taxonomy_tc_category() {

    $labels = array(
        'name' => 'Poker Game Categories',
        'singular_name' => 'Poker Game Category',
        'search_items' => 'Search Poker Game Categories',
        'popular_items' => 'Popular Poker Game Categories',
        'all_items' => 'All Poker Game Categories',
        'parent_item' => 'Parent Poker Game Category',
        'parent_item_colon' => 'Parent Poker Game Category:',
        'edit_item' => 'Edit Poker Game Category',
        'update_item' => 'Update Poker Game Category',
        'add_new_item' => 'Add New Poker Game Category',
        'new_item_name' => 'New Poker Game Category',
        'separate_items_with_commas' => 'Separate Poker Game categories with commas',
        'add_or_remove_items' => 'Add or remove Poker Game categories',
        'choose_from_most_used' => 'Choose from the most used Poker Game categories',
        'menu_name' => 'Poker Game Categories'
    );

    $args = array( 
        'labels' => $labels,
        'public' => true,
        'show_in_nav_menus' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_tagcloud' => false,
        'hierarchical' => true,
        'rewrite' => true,
        'query_var' => true
    );

    register_taxonomy( 'tc_category', array('pkr_game'), $args );
}

?>