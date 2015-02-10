<?php

global $EM_Event, $post, $bp;

$hours_format = em_get_hour_format();

$required = apply_filters('em_required_html','<i>*</i>');

// BP group game settings
$group_id = bp_get_current_group_id();

$start_time = groups_get_groupmeta( $group_id, 'pkr_default_start' );
$disable_end_date = groups_get_groupmeta( $group_id, 'pkr_disable_end_date' );
$disable_end_time = groups_get_groupmeta( $group_id, 'pkr_disable_end_time' );
?>

<div class="event-form-when" id="em-form-when">

	<p class="em-date-range">
<?php if (!$disable_end_date) { ?>
		<?php _e ( 'From ', 'dbem' ); ?>					
<?php } 
	else {
		_e ('Date', 'dbem');
	}
?>	
		<input class="em-date-start em-date-input-loc" type="text" />

		<input class="em-date-input" type="hidden" name="event_start_date" value="<?php echo $EM_Event->event_start_date ?>" />

<?php if (!$disable_end_date) { ?>
	
		<?php _e('to','dbem'); ?>

		<input class="em-date-end em-date-input-loc" type="text" />

		<input class="em-date-input" type="hidden" name="event_end_date" value="<?php echo $EM_Event->event_end_date ?>" />
<?php } ?>		

	</p>

	<p class="em-time-range">

		<span class="em-event-text"><?php _e('Game starts at','dbem'); ?></span>

		<input id="start-time" class="em-time-input em-time-start" type="text" size="8" maxlength="8" name="event_start_time" value="<?php echo date( $hours_format, $EM_Event->start ); ?>" />
<?php if (!$disable_end_time) { ?>
		<?php _e('to','dbem'); ?>

		<input id="end-time" class="em-time-input em-time-end" type="text" size="8" maxlength="8" name="event_end_time" value="<?php echo date( $hours_format, $EM_Event->end ); ?>" />

		<?php _e('All day','dbem'); ?> <input type="checkbox" class="em-time-all-day" name="event_all_day" id="em-time-all-day" value="1" <?php if(!empty($EM_Event->event_all_day)) echo 'checked="checked"'; ?> />
<?php } ?>	
	</p>
<?php if (!$disable_end_date) { ?>
	<span id='event-date-explanation'>

	<?php _e( 'This event spans every day between the beginning and end date, with start/end times applying to each day.', 'dbem' ); ?>

	</span>
<?php } ?>	
</div>  

<?php if( false && get_option('dbem_recurrence_enabled') && $EM_Event->is_recurrence() ) : //in future, we could enable this and then offer a detach option alongside, which resets the recurrence id and removes the attachment to the recurrence set ?>

<input type="hidden" name="recurrence_id" value="<?php echo $EM_Event->recurrence_id; ?>" />

<?php endif;