<?php

//Course Meta fields

add_action('admin_menu', 'lmw_course_meta');
// global $post;
// $post_id = $post->ID;

function lmw_course_meta() {
	add_meta_box('lmw_course_meta_box', 'Course Information', 'lmw_course_meta_inputs', 'lmw_course', 'side', 'high');
}

function lmw_course_meta_inputs(){
	global $post;
	$post_id = $post->ID;
	$awarding_bodies = get_posts('post_type=lmw_awarder&order=ASC&posts_per_page=-1');
	// echo '<pre>';
	// print_r($awarding_bodies);
	// echo '</pre>';
	$current_awarding = get_post_meta( $post_id, 'awarding_body', true );
	$current_level = get_post_meta( $post_id, 'award_level', true );
	
$args = array('post_type' => 'lmw_provider',
		'order' => ASC,
		'suppress_filters' => false,
		'posts_per_page' => -1
		);	

	$providers = get_posts($args);
	$current_providers = get_post_meta( $post_id, 'provider', true );
	$meta_type_awarding = 'awarding_body';
	$meta_type_level = 'award_level';
	$meta_type_provider = 'provider';
	?>
	<?php wp_nonce_field( 'save_meta', 'save_meta_nonce' ); ?>
	<fieldset>
		<label for "<?php echo $meta_type_awarding; ?>">Awarding Body</label>
		<?php if ($awarding_bodies) { ?>
		<p>
			<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_awarding; ?>" />
			<select name="<?php echo $meta_type_awarding; ?>" id="<?php echo $meta_type_awarding; ?>">
				<option class="<?php echo $meta_type_awarding; ?>" name="<?php echo $meta_type_awarding; ?>" value="none">--No awarding body--</option>
				<?php foreach ($awarding_bodies as $awarding_body) { 
					$acronym = get_post_meta( $awarding_body->ID, 'acronym', true );
					 ?>
				<option class="<?php echo $meta_type_awarding; ?>" name="<?php echo $meta_type_awarding; ?>" value="<?php echo $awarding_body->ID; ?>" <?php if ($current_awarding == $awarding_body->ID) {echo 'selected';}?>><?php echo $acronym; ?></option>
				<?php }
	// echo 'CAL = ';
	// print_r($current_level);
	// echo '<br />';
				?>
			</select>
		</p>
		<div id="<?php echo $meta_type_level; ?>">
			<hr />
			<label for "<?php echo $meta_type_level; ?>">Award Levels</label>
			<p>
				<?php $i = 1; while ($i <= 7) { ?>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_level; ?>" />
				<label><?php echo $i; ?></label>&nbsp;<input type="checkbox" name="<?php echo $meta_type_level; ?>[]" value="<?php echo $i; ?>"
				<?php if ($current_level) {
					if (in_array($i, $current_level)){echo 'checked';}
				}
				?>/>
				<?php $i++;
				}?>
			</p>
		</div>
	<?php } else {
		echo '<p>You haven\'t defined any <a href="edit.php?post_type=lmw_awarder">Awarding Bodies</a> yet</p>';
	} ?>
	<div id="<?php echo $meta_type_provider; ?>">
		<hr />
		<label for "<?php echo $meta_type_provider; ?>">Providers</label>
		<p>
			<?php
				if ($providers) { ?>
			<ul>
				<?php foreach ($providers as $provider) { ?>
				<li>
					<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_provider; ?>" />
					<input type="checkbox" name="<?php echo $meta_type_provider; ?>[]" value="<?php echo $provider->ID; ?>"
					<?php if ($current_providers) {
						if (in_array($provider->ID, $current_providers)){echo ' checked';}
					}?>
					/>&nbsp;
					<label><?php echo $provider->post_title; ?></label>
				</li>
				<?php } ?>
			</ul>
			<?php } else {
				echo '<p>You haven\'t defined any <a href="edit.php?post_type=lmw_provider">Training Providers</a> yet</p>';
			}?>
		</p>
	</div>
</fieldset>
<?php
}


//AJAX to validate course info before publishing - ie, ensure there is at least one provider and ONLY one category.
//adapted from http://wordpress.stackexchange.com/questions/42013/prevent-post-from-being-published-if-custom-fields-not-filled
// add_action('admin_enqueue_scripts-post.php', 'ep_load_jquery_js');
// add_action('admin_enqueue_scripts-post-new.php', 'ep_load_jquery_js');
// function ep_load_jquery_js(){
// 	global $post;
// 	if ( $post->post_type == 'lmw_course' ) {
// 		wp_enqueue_script('jquery');
// 	}
// }

add_action('admin_head-post.php','course_validate_categories_providers');
add_action('admin_head-post-new.php','course_validate_categories_providers');
function course_validate_categories_providers(){
	global $post;
	if ( is_admin() && $post->post_type == 'lmw_course' ){
		?>
		<script language="javascript" type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#publish').click(function() {
				if(jQuery(this).data("valid")) {
					return true;
				}
				var form_data = jQuery('#post').serializeArray();
				var data = {
					action: 'course_presubmit_validation',
					security: '<?php echo wp_create_nonce( 'pre_publish_validation' ); ?>',
					form_data: jQuery.param(form_data)
				};
				jQuery.post(ajaxurl, data, function(response) {
					if (response.indexOf('true') > -1 || response == true) {
						jQuery("#publish").data("valid", true).submit();
						jQuery('#publish').trigger('click');
					} else {
						alert("Error: "+response);
						jQuery("#publish").data("valid", false);
					}
	                    //hide loading icon, return Publish button to normal
	                    jQuery('#ajax-loading').hide();
	                    jQuery('.spinner').hide();
	                    jQuery('#publish').removeClass('button-primary-disabled');
	                    jQuery('#save-post').removeClass('button-disabled');
	                });
				return false;
			});
		});
		</script>
		<?php
	}
}
add_action('wp_ajax_course_presubmit_validation', 'course_presubmit_validation');
function course_presubmit_validation() {
//simple Security check
	check_ajax_referer( 'pre_publish_validation', 'security' );
//convert the string of data received to an array
//from http://wordpress.stackexchange.com/a/26536/10406
	parse_str( $_POST['form_data'], $vars );
	$course_cats = $vars['tax_input']['tc_category'];
	$course_cats = count($course_cats);
//check that are actually trying to publish a post
	if ( $vars['post_status'] == 'publish' || 
		(isset( $vars['original_publish'] ) && 
			in_array( $vars['original_publish'], array('Publish', 'Schedule', 'Update') ) ) ) {
	//$course_cats is 2 because there is always a '0' entry, so this is looking for ONE entry
		if ( $course_cats > 2 ) {
			_e('Please insert ONE category only');
			die();
		}
		if ( $course_cats < 2 ) {
			_e('Please insert a category');
			die();
		}
		elseif ( empty ($vars['provider'] ) ) {
			_e('Please insert at least one provider');
			die();
		}
	}

//everything ok, allow submission
	echo 'true';
	die();
}
?>