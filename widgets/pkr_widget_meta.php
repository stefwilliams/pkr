<?php
//tweak default 'meta' widget

function remove_meta_widget() {
    unregister_widget('WP_Widget_Meta');
    register_widget('WP_Widget_Meta_Mod');
}

add_action( 'widgets_init', 'remove_meta_widget' );

/**
 * Meta widget class
 *
 * Displays log in/out, RSS feed links, etc.
 *
 * @since 2.8.0
 */
class WP_Widget_Meta_Mod extends WP_Widget {

function __construct() {
$widget_ops = array('classname' => 'widget_meta', 'description' => __( "Log in/out, admin, feed and WordPress links") );
parent::__construct('meta', __('Meta'), $widget_ops);
}

function widget( $args, $instance ) {
extract($args);
$title = apply_filters('widget_title', empty($instance['title']) ? __('Meta') : $instance['title'], $instance, $this->id_base);

echo $before_widget;
if ( $title )
echo $before_title . $title . $after_title;
?>
<ul>
<?php wp_register(); ?>
<li><?php wp_loginout(); ?></li>
<?php wp_meta(); ?>
</ul>
<?php
echo $after_widget;
}

function update( $new_instance, $old_instance ) {
$instance = $old_instance;
$instance['title'] = strip_tags($new_instance['title']);

return $instance;
}

function form( $instance ) {
$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
$title = strip_tags($instance['title']);
?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
}
}