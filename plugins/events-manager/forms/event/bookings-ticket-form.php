<?php 

/* 

 * Used for both multiple and single tickets. $col_count will always be 1 in single ticket mode, and be a unique number for each ticket starting from 1 

 * This form should have $EM_Ticket and $col_count available globally. 

 */

global $col_count, $EM_Ticket, $bp;

$group_id = bp_get_current_group_id();
$default_buy_in = groups_get_groupmeta( $group_id, 'pkr_default_buyin' );
//replace 'Standard Ticket' text for a new event
if( count($EM_Tickets->event_id) == NULL ){
		$EM_Ticket->ticket_name = "Standard Buy-in";		
}
if ($EM_Ticket->ticket_price == NULL) {
	$EM_Ticket->ticket_price = $default_buy_in;
}
?>

<div class="em-ticket-form">

	<input type="hidden" name="em_tickets[<?php echo $col_count; ?>][ticket_id]" class="ticket_id" value="<?php echo esc_attr($EM_Ticket->ticket_id) ?>" />

	<div class="em-ticket-form-main">

		<div class="ticket-name">

			<label title="<?php __('Buy-in type.','dbem'); ?>"><?php _e('Buy-in','dbem') ?></label>

			<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_name]" value="<?php echo esc_attr($EM_Ticket->ticket_name) ?>" class="ticket_name" />

		</div>

		<div class="ticket-price"><label><?php _e('Cost','dbem') ?></label><input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_price]" class="ticket_price" value="<?php echo esc_attr($EM_Ticket->get_price_precise()) ?>" /></div>

		<div class="ticket-spaces">

			<label title="<?php __('Enter a maximum number of spaces (required).','dbem'); ?>"><?php _e('Spaces','dbem') ?></label>

			<input type="text" name="em_tickets[<?php echo $col_count; ?>][ticket_spaces]" value="<?php echo esc_attr($EM_Ticket->ticket_spaces) ?>" class="ticket_spaces" />

		</div>

	</div>

</div>	