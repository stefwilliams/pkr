<?php
//remove elements from profile page
function pkr_remove_em_profile_nav() {
	global $bp;
	if (function_exists('bp_core_remove_subnav_item')) {
		bp_core_remove_subnav_item($bp->groups->slug,'group-events');
		bp_core_remove_subnav_item($bp->settings->slug,'profile');
		
	}

	//remove top level Events tab from profile page
	if (function_exists('bp_core_remove_nav_item')) {
		bp_core_remove_nav_item( 'events' );
	}

}
add_action( 'init', 'pkr_remove_em_profile_nav' );


add_filter ('bp_get_search_default_text', 'pkr_bp_regex_members', 20, 2);
function pkr_bp_regex_members($string) {
	$bp_terms = array (
		'Member',
		'Members',
		'member',
		'members',
		);
	$pkr_terms = array (
		'Player',
		'Players',
		'player',
		'players',
		);
	$string = str_replace($bp_terms, $pkr_terms, $string);
	return $string;
}
//change all references to groups in email messages
add_filter('groups_notification_group_updated_subject', 'pkr_regex_groups', 20, 1);
add_filter('groups_notification_group_updated_message', 'pkr_regex_groups', 20, 1);

add_filter('groups_notification_new_membership_request_subject', 'pkr_regex_groups', 20, 1);
add_filter('groups_notification_new_membership_request_message', 'pkr_regex_groups', 20, 1);

add_filter('groups_notification_membership_request_completed_subject', 'pkr_regex_groups', 20, 1);
add_filter('groups_notification_membership_request_completed_message', 'pkr_regex_groups', 20, 1);

add_filter('groups_notification_promoted_member_subject', 'pkr_regex_groups', 20, 1);
add_filter('groups_notification_promoted_member_message', 'pkr_regex_groups', 20, 1);

add_filter('groups_notification_group_invites_subject', 'pkr_regex_groups', 20, 1);
add_filter('groups_notification_group_invites_message', 'pkr_regex_groups', 20, 1);

//change group search form
add_filter('bp_directory_groups_search_form', 'pkr_regex_groups', 20, 1);
//change all occurences of group in group status messages
add_filter('bp_group_status_message', 'pkr_regex_groups', 20, 1);
function pkr_regex_groups($string) {
	$bp_terms = array (
		'Group',
		'Groups',
		'group',
		'groups',
		);
	$pkr_terms = array (
		'League',
		'Leagues',
		'league',
		'leagues'
		);
	$string = str_replace($bp_terms, $pkr_terms, $string);
	return $string;
}

function pkr_regex_events($string) {
	$bp_terms = array (
		'Event',
		'Events',
		'event',
		'events',
		);
	$pkr_terms = array (
		'Game',
		'Games',
		'game',
		'games'
		);
	$string = str_replace($bp_terms, $pkr_terms, $string);
	return $string;
}


//change tab names, Group(s) -> League(s), Event(s) -> Game(s), Member(s) -> Player(s)
add_action('bp_init', 'pkr_change_group_tab_names', 999);
function pkr_change_group_tab_names() {
	global $bp;

    $bp->bp_nav['groups']['name'] = pkr_regex_groups($bp->bp_nav['groups']['name']);
    $bp->bp_nav['events']['name'] = pkr_regex_events($bp->bp_nav['events']['name']);

    $bp->bp_options_nav['activity']['groups']['name'] = pkr_regex_groups($bp->bp_options_nav['activity']['groups']['name']);
    // $bp->members->name = "Players";

  if (isset($bp->groups->current_group->slug) && $bp->groups->current_group->slug == $bp->current_item) {
    $bp->bp_options_nav[$bp->groups->current_group->slug]['members']['name'] = pkr_bp_regex_members($bp->bp_options_nav[$bp->groups->current_group->slug]['members']['name']);
    $bp->bp_options_nav[$bp->groups->current_group->slug]['events']['name'] = pkr_regex_events($bp->bp_options_nav[$bp->groups->current_group->slug]['events']['name']);
    $bp->bp_nav['groups']['name'] = pkr_regex_groups($bp->bp_nav['groups']['name']);
  }    
}

//change text on Groups listing page, Group(s) -> League(s)
add_filter('bp_get_groups_pagination_count', 'pkr_change_group_count', 20, 4);
function pkr_change_group_count($content_in, $from_num, $to_num, $total) {
	$string = sprintf( _n( 'Viewing 1 league', 'Viewing %1$s - %2$s of %3$s leagues', $total, 'buddypress' ), $from_num, $to_num, $total );
	return $string;
}

//change text for "Create a Group" button
add_filter( 'bp_get_group_create_button', 'pkr_change_group_button', 20, 1);
function pkr_change_group_button($button_args){;
	$button_args['link_text'] = pkr_regex_groups($button_args['link_text']);
	$button_args['link_title'] = pkr_regex_groups($button_args['link_title']);
	return $button_args;
}

//change title for Groups landing page
add_filter('bp_get_directory_title', 'pkr_change_directory_title', 20, 2);
function pkr_change_directory_title($title, $component) {
	print_r($bp);
	if ($component == "groups") {
		$title = pkr_regex_groups($title);
	}
	return $title;
}

?>