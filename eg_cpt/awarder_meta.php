<?php
//Awarding Body Meta fields

add_action('admin_menu', 'lmw_awarder_meta');

function lmw_awarder_meta() {
	add_meta_box('lmw_awarder_meta_box', 'Awarding Body Information', 'lmw_awarder_meta_inputs', 'lmw_awarder', 'normal', 'high');
}

function lmw_awarder_meta_inputs(){
	global $post;
	$post_id = $post->ID;
	$current_acronym = get_post_meta( $post_id, 'acronym', true );
	$current_url = get_post_meta( $post_id, 'url', true );
	$meta_type_acronym = 'acronym';
	$meta_type_url = 'url';
	?>
	<?php wp_nonce_field( 'save_meta', 'save_meta_nonce' ); ?>
	<fieldset>
		<div style="float:left; width:35%">
			<h4>Acronym</h4>
			<p>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_acronym; ?>" />
				<input type="text" name="<?php echo $meta_type_acronym; ?>" value="<?php echo $current_acronym?>" />
			</p>
		</div>
		<div style="float:right;width:60%">
			<h4>URL</h4>
			<p>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_url; ?>" />
				<input type="url" name="<?php echo $meta_type_url; ?>" value="<?php echo $current_url?>" size="60" />
			</p>
		</div>
	</fieldset>
	<?php
}
?>