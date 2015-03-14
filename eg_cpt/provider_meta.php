<?php
//Awarding Body Meta fields

add_action('admin_menu', 'lmw_provider_meta');
add_action('admin_menu', 'lmw_provider_address_meta');

// reposition the editor below Contact details

add_action( 'add_meta_boxes', 'lmw_provider_reposition_editor', 0 );
	function lmw_provider_reposition_editor() {
		global $_wp_post_type_features;
		if (isset($_wp_post_type_features['lmw_provider']['editor']) && $_wp_post_type_features['lmw_provider']['editor']) {
			unset($_wp_post_type_features['lmw_provider']['editor']);
			add_meta_box(
				'description_sectionid',
				__('About Us'),
				'lmw_provider_custom_editor',
				'lmw_provider', 'normal', 'high'
			);
		}
	}
	function lmw_provider_custom_editor( $post ) {
		the_editor($post->post_content);
	}

function lmw_provider_meta() {
	add_meta_box('lmw_provider_meta_box', 'Training Information', 'lmw_provider_meta_inputs', 'lmw_provider', 'side', 'high');
}

function lmw_provider_meta_inputs(){
	global $post;
	$post_id = $post->ID;

	$current_course_url = get_post_meta( $post_id, 'course_url', true );		
	$meta_type_course_url = 'course_url';

	$current_cs_url = get_post_meta( $post_id, 'case_study_url', true );	
	$meta_type_cs_url = 'case_study_url';

	$current_cs_title = get_post_meta( $post_id, 'case_study_title', true );	
	$meta_type_cs_title = 'case_study_title';


	$current_regions = get_post_meta( $post_id, 'regions', true );
	$meta_type_regions = 'regions';
	?>
	<?php wp_nonce_field( 'save_meta', 'save_meta_nonce' ); ?>
		<fieldset>
			<h4>Regions Covered</h4>
			<ul>
						<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_regions; ?>" />
					<li>
						<input type="checkbox" name="<?php echo $meta_type_regions; ?>[]" value="North Wales"
					<?php if ($current_regions) {
						if (in_array('North Wales',$current_regions)){echo ' checked';}
					}?>
						/>&nbsp;
						<label>North Wales</label>
					</li>
					<li>
						<input type="checkbox" name="<?php echo $meta_type_regions; ?>[]" value="Mid Wales"
					<?php if ($current_regions) {
						if (in_array('Mid Wales',$current_regions)){echo ' checked';}
					}?>
						/>&nbsp;
						<label>Mid Wales</label>
					</li>
					<li>
						<input type="checkbox" name="<?php echo $meta_type_regions; ?>[]" value="South West Wales"
					<?php if ($current_regions) {
						if (in_array('South West Wales',$current_regions)){echo ' checked';}
					}?>
						/>&nbsp;
						<label>South West Wales</label>
					</li>	
					<li>
						<input type="checkbox" name="<?php echo $meta_type_regions; ?>[]" value="South East Wales"
					<?php if ($current_regions) {
						if (in_array('South East Wales',$current_regions)){echo ' checked';}
					}?>
						/>&nbsp;
						<label>South East Wales</label>
					</li>
			</ul>
			<h4>Case Study Details</h4>

			<p>Case Study Title:<br>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_cs_title; ?>" />
				<input type="text" name="<?php echo $meta_type_cs_title; ?>" value="<?php echo $current_cs_title; ?>" size="40" />


			</p>
			<p>Case Study URL:<br>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_cs_url; ?>" />
				<input type="url" name="<?php echo $meta_type_cs_url; ?>" value="<?php echo $current_cs_url; ?>" size="40" />


			</p>
			<h4>Courses URL</h4>
			<p>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_course_url; ?>" />
				<input type="url" name="<?php echo $meta_type_course_url; ?>" value="<?php echo $current_course_url; ?>" size="40" />
			</p>
			<hr />
	<?php
	$current_kitemark = get_post_meta( $post_id, 'kitemark', true );
	$meta_type_kitemark = 'kitemark';
	?>
			<p>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_kitemark; ?>" />
				<input type="checkbox" name="<?php echo $meta_type_kitemark; ?>" value="approved"
				<?php if ($current_kitemark == 'approved') {
					echo ' checked';
				}
				?>
				/>&nbsp;
				<label for "<?php echo $meta_type_kitemark; ?>">Kitemark Approved?</label>
			</p>
		</fieldset>
<?php
}

function lmw_provider_address_meta() {
	add_meta_box('lmw_provider_address_meta_box', 'Provider Contact Details', 'lmw_provider_address_meta_inputs', 'lmw_provider', 'normal', 'high');
}

function lmw_provider_address_meta_inputs(){
	global $post;
	$post_id = $post->ID;
	?>
		<fieldset>
<div style="float:left;width:60%;">
	<?php
	$current_address = get_post_meta( $post_id, 'address', true );
	$meta_type_address = 'address';
	?>
	<h4>Provider Office Address</h4>
			<p>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_address; ?>" />
				<textarea rows="5" cols="50" name="<?php echo $meta_type_address; ?>"><?php echo $current_address; ?></textarea>
			</p>
	<?php
	$current_postcode = get_post_meta( $post_id, 'postcode', true );
	$meta_type_postcode = 'postcode';
	?>
			<label for "<?php echo $meta_type_postcode; ?>">Postcode</label>
			<p>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_postcode; ?>" />
				<input type="text" name="<?php echo $meta_type_postcode; ?>" value="<?php echo $current_postcode; ?>" />
			</p>
	<?php
	$current_telephone = get_post_meta( $post_id, 'telephone', true );
	$meta_type_telephone = 'telephone';
	?>
			<label for "<?php echo $meta_type_telephone; ?>">Telephone</label>
			<p>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_telephone; ?>" />
				<input type="text" name="<?php echo $meta_type_telephone; ?>" value="<?php echo $current_telephone; ?>" />
			</p>
	<?php
	$current_website = get_post_meta( $post_id, 'website', true );
	$meta_type_website = 'website';
	?>
			<label for "<?php echo $meta_type_website; ?>">Website</label>
			<p>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_website; ?>" />
				<input type="url" name="<?php echo $meta_type_website; ?>" value="<?php echo $current_website; ?>" size="60" />
			</p>
</div>
<div style="float:left; width:35%;">
	<h4>Main Contact Person</h4>
	<?php
	$current_contact_name = get_post_meta( $post_id, 'contact_name', true );
	$meta_type_contact_name = 'contact_name';
	?>
			<label for "<?php echo $meta_type_contact_name; ?>">Contact Name</label>
			<p>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_contact_name; ?>" />
				<input type="text" name="<?php echo $meta_type_contact_name; ?>" value="<?php echo $current_contact_name; ?>" size="40" />
			</p>
	<?php
	$current_contact_email = get_post_meta( $post_id, 'contact_email', true );
	$meta_type_contact_email = 'contact_email';
	?>
			<label for "<?php echo $meta_type_contact_email; ?>">Contact Email</label>
			<p>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_contact_email; ?>" />
				<input type="email" name="<?php echo $meta_type_contact_email; ?>" value="<?php echo $current_contact_email; ?>" size="40" />
			</p>
	<?php
	$current_mobile = get_post_meta( $post_id, 'mobile', true );
	$meta_type_mobile = 'mobile';
	?>
			<label for "<?php echo $meta_type_mobile; ?>">Mobile</label>
			<p>
				<input type="hidden" name="meta_save_type[]" value="<?php echo $meta_type_mobile; ?>" />
				<input type="text" name="<?php echo $meta_type_mobile; ?>" value="<?php echo $current_mobile; ?>" />
			</p>
</div>
		</fieldset>
<?php }	?>