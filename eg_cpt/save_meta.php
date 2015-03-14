<?php

//this is the main function that saves the post meta for all the custom types.

function save_course_meta ($post_id, $post) {

     //error_log(var_export($_POST,true));

    if ( !isset( $_POST['save_meta_nonce'] ) || !wp_verify_nonce( $_POST['save_meta_nonce'], 'save_meta' ) )
        return $post_id;

    // Get the post type object.
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;

    //use the hidden meta_save_type field to pull in the array of meta content to be saved.
    $meta_save_types = $_POST['meta_save_type'];


    if (is_array($meta_save_types)) {
        foreach ($meta_save_types as $meta_save_type) {

            //save custom_meta values
            $new_ab_value = $_POST[$meta_save_type];
            /* Get the meta key. */
            $ab_key = $meta_save_type;
            /* Get the meta value of the custom field key. */
            $ab_value = get_post_meta( $post_id, $ab_key, true );
            /* If a new meta value was added and there was no previous value, add it. */
            if ( $new_ab_value && '' == $ab_value ){
                add_post_meta( $post_id, $ab_key, $new_ab_value, true );
            }
            /* If the new meta value does not match the old value, update it. */
            elseif ( $new_ab_value && $new_ab_value != $ab_value ) {
                update_post_meta( $post_id, $ab_key, $new_ab_value );
            }
            /* If there is no new meta value but an old value exists, delete it. */
            elseif ( '' == $new_ab_value && $ab_value ) {
                delete_post_meta( $post_id, $ab_key, $ab_value );
            }
        }
    }
}

/* Save post meta on the 'save_post' hook. */
add_action( 'save_post', 'save_course_meta', 10, 2 );

?>