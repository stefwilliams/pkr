<?php

class Pkr_Game {

	public static $league_id;	
	public static $starting_pot;
	public static $event_id;
	public $buy_in_price;
	public $in_progress = 0;
	public $is_complete = 0;
	public $rebuy_flags = 0;
	public $total_rebuy_amount = 0;
	public $set_by;
	public $last_refresh_nonce;
	public $players_playing = array();
	public $players_not_playing = array();
	public $player_positions = array();


	public function __construct($league_id, $event_id) {

		if ($_REQUEST['action'] == 'reset_game') {
			// $event_id = $_REQUEST['event_id'];
			// $event_id = $this->event_id;
			unset($_SESSION['pkr_game_'.$event_id]);
		}
		echo "<pre>construct";
		print_r($_REQUEST['buy_in']);
		echo "</pre>";
		$this->buy_in_price = $_REQUEST['buy_in'];
		
		
		$all_players = array();
		$all_players = $this->pkr_all_players();
		$this->event_id = $event_id;
		$this->league_id = $league_id;
// $this->pass1 = $event_id;

		// $this->league_id = bp_get_group_id();
		// $this->event_id = $_REQUEST['event_id'];
		$this->players_playing = $all_players['players_playing'];
		$this->players_not_playing = $all_players['players_not_playing'];
		$this->set_by[microtime()] = "construct:pp:".count($this->players_playing).":bi:".$this->buy_in_price;
		$this->set_starting_pot();
	}

	public function __destruct() {
		$this->set_by[microtime()] = "destruct";
	}

	public function route_request() {
		$this->save_to_session();

		if ($_REQUEST['action'] == 'modify_list') {
			$this->modify_player_list();
			$this->show_player_confirm_list();
			return;
		}
		elseif ($_REQUEST['action'] == 'reset_game') {
			$this->__construct($this->league_id, $this->event_id);
			$this->game_in_progress();
			return;
		}
		elseif ($_REQUEST['action'] == 'register_and_book') {
			$this->register_and_book();
			return;
		}
		elseif ($_REQUEST['action'] == 'start_game') {

			$this->game_in_progress();
			return;
		}
		elseif ($_REQUEST['action'] == "eliminated") {
			$this->eliminated();
			return;
		}
		elseif ($_REQUEST['action'] == 'eliminate') {
			$this->eliminate();
			return;
		}
		else {
			$this->show_player_confirm_list();
		}
	}

	public function save_to_session() {
		$this->set_by[microtime()] = "save_to_session";
		$_SESSION['pkr_game_'.$this->event_id] = $this;
	}

	public function set_starting_pot() {	
		$starting_pot = number_format((count($this->players_playing) * $this->buy_in_price), 2);
		$this->set_by[microtime()] = "set_starting_pot@".$starting_pot;
		print_r("ssp");
		print_r($_REQUEST);
		$this->starting_pot = $starting_pot;
	}
//puts all currently booked members into 'players_playing' and all other league members into 'players_not_playing'
//'players_not_playing' array is irrelevant and has been commented out, (for now)
	public function pkr_all_players() {
		global $EM_Event, $bp;
		$all_players = array();
		$exclusions = NULL;
		// get currently booked players
		$players = $EM_Event->get_bookings();
		if (is_array($players->bookings) && !empty($players->bookings)) {
			$exclusions = array();
			// add already booked players to players array and include in exclusions array
			foreach ($players as $player) {	
				// print_r($player);
				$all_players['players_playing'][$player->person_id]['display_name'] = $player->get_person()->get_name();
				$all_players['players_playing'][$player->person_id]['booking_id'] = $player->booking_id;
				array_push($exclusions, $player->person_id);
			}			
		}
		// define args to return the whole group
		$group_args = array (
			'exclude_admins_mods' => false,
			'exclude' => $exclusions,
			);
			// return all group members using args
		$group_members = groups_get_group_members($group_args);
			// refine array to relevant part
		$group_members = $group_members['members'];

		foreach ($group_members as $group_member) {
			$all_players['players_not_playing'][$group_member->ID]['display_name'] = $group_member->display_name;
		}
		return $all_players;
	}

	public function modify_player_list() {
		global $EM_Booking;
		$ticket_id = $_POST['ticket_id'];
		// $buy_in = number_format($_POST['buy_in'], 2);

		$people_to_add = array();
		$people_to_add = $_POST['add_player'];
		$people_to_remove = array();
		$people_to_remove = $_POST['remove_booking'];
		if ($people_to_remove) {
			foreach ($people_to_remove as $booking_id) {
				$return = $this->pkr_em_delete_single_booking($this->event_id, $booking_id);
				if ($return) {
					continue;
				}
				else {
					print_r("There was a problem removing the booking.");
				}
			}
		}
		if ($people_to_add) {
			foreach ($people_to_add as $person_id) {
				$return = $this->pkr_em_add_single_booking($this->event_id, $person_id, $ticket_id);
				if ($return) {
					continue;
				}
				else {
					print_r("There was a problem making the booking.");
				}
			}
		}
	}

	public function pkr_em_delete_single_booking($event_id, $booking_id) {
		global $EM_Booking;		
		$booking = new EM_Booking;
		$booking->booking_id = $booking_id;
		$booking->event_id = $event_id;
		$deleted = $booking->delete();

//Get details of user being moved from current object
		$players_playing = $this->players_playing;
		foreach ($players_playing as $person_id => $data) {
			if ((isset($data['booking_id'])) && ($data['booking_id'] == $booking_id)) {
				$person_id_to_move = $person_id;
				$person_details = $person_id_to_move['display_name'];
			}
		}

		if (is_int($person_id_to_move)) {
			$move_details = $players_playing[$person_id_to_move];
		}

		else {
			print_r("ERROR!: Something went wrong.");
		}

//If deletion was successful and we have details of person, change object

		if ($deleted && $move_details) {
			$this->players_not_playing[$person_id_to_move]['display_name'] = $move_details['display_name'];
			if (isset($this->players_playing[$person_id_to_move])) {
				unset($this->players_playing[$person_id_to_move]);
			}

			$this->set_by[microtime()] = "pkr_em_delete_single_booking";

			// $_SESSION['pkr_game_'.$this->event_id] = $this;
			return true;
		}
		else {
			return NULL;
		}

	}


	public function pkr_em_add_single_booking($event_id, $person_id, $ticket_id) {
		global $EM_Booking;

		$_REQUEST = array(
			'action' => 'booking_add',
			'event_id' => $event_id,
			'em_tickets' => array (
				$ticket_id => array(
					'spaces' => 1,
					),	
				),
			);

		$booking = new EM_Booking;
		$result = $booking->get_post();	
		$booking->person_id = $person_id;
		$saved = $booking->save(false);

		if ($saved) {
			$this->players_playing[$person_id]['display_name'] = $booking->person->data->display_name;
			$this->players_playing[$person_id]['booking_id'] = $booking->booking_id;
			if (isset($this->players_not_playing[$person_id])) {
				unset($this->players_not_playing[$person_id]);
			}

			$this->set_by[microtime()] = "pkr_em_add_single_booking";		

			// $_SESSION['pkr_game_'.$this->event_id] = $this;
			return true;
		}
		else {
			return NULL;
		}

	}

	public function register_and_book() {
		$user_name = $_POST['user_name'];
		$email = $_POST['email'];
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];

		if (!$user_name || !$email || !$first_name || !$last_name) {
			echo "<strong>Error!</strong> Please complete all fields on the registration form.";
			return;
		}

		$ticket_id = $_POST['ticket_id'];
		// $buy_in = $_POST['buy_in'];

		$event_id = $this->event_id;

		$user_id = register_new_user($user_name, $email);

		if (!is_wp_error($user_id)) {
			$add_name = update_user_meta( $user_id, 'first_name', $first_name);
			$add_surname = update_user_meta( $user_id, 'last_name', $last_name);

			$add_displayname = wp_update_user( array('ID' => $user_id, 'display_name' => $first_name." ".$last_name) );
			$group_add = groups_join_group($this->league_id, $user_id);
			$booked = $this->pkr_em_add_single_booking($event_id, $user_id, $ticket_id);
			if ($booked && $group_add && $add_name && $add_surname && $add_surname && !is_wp_error($add_displayname)) {
				echo "User successfully registered and added to game.";
				$this->show_player_confirm_list();
				return;
			}
			else {
				echo "Something went wrong: Please print this screen and send it to the devs:";
				echo "<pre>";
				echo "<br />$add_name<br />";
				print_r($add_name);
				echo "<br />$add_surname<br />";
				print_r($add_surname);
				echo "<br />$add_dsplayname<br />";
				print_r($add_dsplayname);
				echo "<br />$group_add<br />";
				print_r($group_add);
				echo "<br />$booked<br />";
				print_r($booked);				
				echo "</pre>";
				return;
			}			
		}
		else {
			echo $user_id->get_error_message();
				$this->show_player_confirm_list();
			return;
		}
	}

	public function game_in_progress() {
		if ((count($this->players_playing) == 0) && ($this->in_progress == 1) && ($_REQUEST['action'] != 'reset_game')) {
			$this->results = new Pkr_Result($this->buy_in_price, $this->rebuy_flags, $this->total_rebuy_amount, $this->player_positions);
			// $this->finalise_results();
			return;
		}

		echo "<h4>Editing Game Results</h4>";
		echo "<p>Click players' names to eliminate them from the game...</p>";
		$this->in_progress = 1;
		$this->set_by[microtime()] = "game_in_progress";
// print_r($_REQUEST);
		// $this->buy_in_price = number_format($_REQUEST['buy_in'],2);		
		$players_playing = $this->players_playing;
		$players_out = $this->player_positions;
		$num_positions = $players_out[0]['position'];

		// echo "num_positions=".$num_positions;

		// echo "<pre style='font-size:x-small;'>";
		// echo "game_in_progress<br />";
		// echo "this<br />";
		// print_r($this);
		// // echo "SESSION<br />";
		// // print_r($_SESSION);
		// echo "</pre>";

		echo "<div class='playing' style='width:50%;float:left;'>";
		echo '<ul class="league-members attending" style="list-style:none;">';
		if ($players_playing) {
			foreach ($players_playing as $player_id => $player) {
				echo '<li>'.get_avatar($player_id, 25).'<a style="border:none;" href="?action=eliminate&amp;player_id='.$player_id.'&amp;event_id='.$this->event_id.'&amp;buy_in='.$this->buy_in_price.'">'
				.'&nbsp;'. 
				$player['display_name']
				.'&nbsp;</a>';
				echo "<br /><span style='font-size:small;'>Heads: ";
				if (count($player['heads_taken']) > 0) {
					echo count($player['heads_taken']).'&nbsp;';
					foreach ($player['heads_taken'] as $defeated_player) {
						echo get_avatar(key($defeated_player), 15).'&nbsp;';
					}
				}
				else { echo "0";}
				echo "</span></li>";
			}
					# code...
		}
		echo "</ul>";
		echo "</div>";


		echo "<div class='not-playing' style='width:50%;float:right;'>";
		echo "<ol>";
		$count = 0;

		while ($count < $num_positions && $num_positions > 0) {
			$pos = $count+1;
			echo "<li class='count".$pos."'>";
			foreach ($players_out as $key => $player) {
				if ($player['killer_id'] != 0) {
					$player_avatar = get_avatar($player['killer_id'], 15);
				}
				else {
					$player_avatar = "X";
				}
				if ($player['rebuy']) {
					$rebuy = "<span style='font-size:x-small'> (Rebuy)</span>";
				}
				else {
					$rebuy = NULL;
				}
				if ($player['position'] == $pos) {
					echo get_avatar($player['user_id'], 25)
					.'&nbsp;'. 
					$player['display_name']
					. $rebuy 
					.'<span style="float:right;font-size:small;">Killed by: '.
					$player_avatar
					.'</span><br /><span style="color:red;font-size:small;">- &pound;'.
					round($player['money_lost'])
					.'</span> <span style="color:green;font-size:small;">+ &pound;'.
					round($player['money_won'])
					.'</span>'.
					'<span style="float:right;font-size:small;">Heads: '.
					count($player['heads_taken'])
					.'</span>';
				}
			}
			echo "</li>";
			$count = $count + 1;			# code...
		}

		echo "</ol>";

		echo "</div>";

		echo "<div style='width:100%;clear:both;text-align:center;'><a href='?action=reset_game&amp;event_id=".$this->event_id."&amp;buy_in=".$this->buy_in_price."'>Reset game</a></div>";
		// $_SESSION['pkr_game_'.$this->event_id] = $this;

		echo "<pre style='font-size:x-small;'>";
		// echo "eliminate (before set new session)<br />";
		// echo "this<br />";
		print_r($this);
		// echo "SESSION<br />";
		// print_r($_SESSION);
		echo "</pre>";		

		return;
	}

	public function eliminate($errors = array()) {
		$eliminated = $_REQUEST['player_id'];
		$userdata = get_userdata( $eliminated );
		echo "<h4>Eliminating Player: ".$userdata->display_name."</h4>";
		// echo "<pre style='font-size:x-small;'>";
		// echo "eliminate (incoming)<br />";
		// echo "this<br />";
		// print_r($this);
		// echo "SESSION<br />";
		// print_r($_SESSION);
		// echo "</pre>";
		$this->in_progress = 1;
		$this->set_by[microtime()] = "eliminate";
		// echo "<pre style='font-size:x-small;'>";
		// print_r($this);
		// print_r($_REQUEST);
		// echo "</pre>";
		// $eliminated = $_REQUEST['player_id'];
		$buy_in_price = $_REQUEST['buy_in'];
		if (is_numeric($buy_in_price) && ($buy_in_price > 0)) {
			$full_rebuy = number_format($buy_in_price, 2);
			$half_rebuy = number_format($buy_in_price / 2, 2);
		}
		// print_r($eliminated);
		//remove the person just eliminated

		$remaining_players = $this->players_playing;
		$max_win = ($this->starting_pot + $this->total_rebuy_amount) / count($remaining_players);
		unset($remaining_players[$eliminated]);
		// echo "<pre style='font-size:x-small;'>";
		// // print_r($players);
		// print_r($remaining_players);
		// echo "</pre>";
		?>
		<form method="post" class="eliminate">
			<?php
			$refresh_nonce = wp_create_nonce( 'eliminate'.$eliminated.time() );
			// $this->last_refresh_nonce = $refresh_nonce;
			?>
			<input type="hidden" name="max_win" value="<?php echo $max_win; ?>">
			<input type="hidden" name="refresh_nonce" value="<?php echo $refresh_nonce; ?>">
			<input type="hidden" name="action" value="eliminated">
			<input type="hidden" name="player_id" value="<?php echo $eliminated ?>">
			<input type="hidden" name="buy_in" value="<?php echo $buy_in_price ?>">
			<p>
				<label for="eliminated_by">Head taken by:</label>
				<select name="eliminated_by">
					<option value="0">--no-one--</option>
					<?foreach ($remaining_players as $user_id => $userdata) {?>
					<option value="<?php echo $user_id;?>"><?php echo $userdata['display_name'];?></option>				
					<?php } ?>
				</select>
			</p>
			<p>
				<label for="rebuy_amount">Rebuy amount: </label>
				<select name="rebuy_amount">
					<option value="no">--no rebuy--</option>
					<option value="<?php echo $half_rebuy?>"><?php echo $half_rebuy?></option>
					<option value="<?php echo $full_rebuy?>"><?php echo $full_rebuy?></option>
				</select>
			</p>			
			<p>
				<label for="money_won">Amount won: </label>
				<input type="text" name="money_won" size="7" style="width:initial;"/>
			</p>
			<p>
				<label for="comment">Comments: </label>
				<textarea name="comment" rows="5" cols="30" style="width:initial;"></textarea>
			</p>
			<button type="submit">Eliminate player</button>
		</form>
		<?php
		echo "<pre style='font-size:x-small;'>";
		// echo "eliminate (before set new session)<br />";
		// echo "this<br />";
		print_r($this);
		// echo "SESSION<br />";
		// print_r($_SESSION);
		echo "</pre>";		
		// $_SESSION['pkr_game_'.$this->event_id] = $this;		

		return;
	}

	public function validate_eliminateform() {
		print_r($_POST);
		$errors = false;
		$rebuy = $_POST['rebuy_amount'];
		$money_won = $_POST['money_won'];
		$max_win = $_POST['max_win'];
		$total_pot = $this->starting_pot + $this->total_rebuy_amount;
		if ($money_won > $max_win) {
			$errors[0]['field'] = "money_won";
			$errors[0]['message'] = "Player cannot win this much money in this position";
			$errors[0]['entered'] = $money_won;
			# code...
		}
		if ($money_won > $total_pot) {
			$errors[0]['field'] = "money_won";
			$errors[0]['message'] = "Money won is more than the total pot!";
			$errors[0]['entered'] = $money_won;
		}
		if (($rebuy > 0) && ($money_won > 0)) {
			$errors[0]['field'] = "rebuy_amount";
			$errors[0]['message'] = "Cannot win money and rebuy at the same time.";
			$errors[0]['entered'] = $rebuy;
		}
		print_r($errors);

	}

	public function eliminated() {
		$errors = $this->validate_eliminateform();
		if ($errors) {
			$this->eliminate($errors);
		}

		$refresh_nonce = $_REQUEST['refresh_nonce'];
		$last_refresh_nonce = $this->last_refresh_nonce;

		if ($refresh_nonce == $last_refresh_nonce) {
			echo "<p>Browser refresh detected - nothing has been updated</p>";
			$refreshed = true;
		}
		
		$eliminated = $_REQUEST['player_id'];
		$killed_by = $_REQUEST['eliminated_by'];
		if (!$refreshed && is_numeric($_REQUEST['rebuy_amount'])) {
			$rebuy = 1;
			$rebuys_to_date = $this->rebuy_flags;
			$this->rebuy_flags = $rebuys_to_date + $rebuy;

			$rebuy_amount = $_REQUEST['rebuy_amount'];
			$total_rebuy_amount = $this->total_rebuy_amount;
			$this->total_rebuy_amount = $total_rebuy_amount + $rebuy_amount;
		}
		else {
			$rebuy = 0;
		}
		if (($_REQUEST['money_won']) && is_numeric($_REQUEST['money_won']) && $_REQUEST['money_won'] > 0) {
			$money_won = $_REQUEST['money_won'];
		}
		else {
			$money_won = 0;
		}
		
		$comment = esc_textarea($_REQUEST['comment']);


		if (!$rebuy) {
			$money_lost = $_REQUEST['buy_in'];
		}
		else {
			$money_lost = $rebuy_amount;
		}
		

		$this->in_progress = 1;
		$this->buy_in_price = $money_lost;
		$this->set_by[microtime()] = "eliminated";

// echo "<pre>";
// echo "before rebuy shunt";
// print_r($this->player_positions);
// echo "</pre>";



		$position = count($this->players_playing) + $rebuy;
		// $position = count($this->players_playing);
		// echo "<h1>".$position."</h1>";

		if ($rebuy && !$refreshed) {
			// echo "done rebuy thing";

			foreach ($this->player_positions as $key=>$player_position) {
				$new_position = $player_position['position'] + 1;
				// $new_position = $new_position;
				$this->player_positions[$key]['position'] = $new_position;
			}


			// $empty = array(
			// 	$position = array());
			// array_splice($this->player_positions, 0, 0, $empty);

		}		

// echo "<pre>";
// echo "after rebuy shunt";
// print_r($this->player_positions);
// echo "</pre>";

			// if ($rebuy) {
			// 	return;
			// }

			// return;			
		$position_array = array (
			'position' => $position,
			'user_id' => $eliminated,
			'display_name' => $this->players_playing[$eliminated]['display_name'],
			'killer_id' => $killed_by,
			'killer_name' => $this->players_playing[$killed_by]['display_name'],
			'heads_taken' => $this->players_playing[$eliminated]['heads_taken'],
			'rebuy' => $rebuy,
			'money_won' => number_format($money_won, 2),
			'money_lost' => number_format($money_lost, 2),
			'comment' => $comment,
			);
		// echo "<pre style='font-size:x-small;'>";
		// echo "this->player_positions";
		// print_r($this->player_positions);
		// echo "position_array";
		// print_r($position_array);
		// echo "</pre>";
//prevent add on page refresh
		if (($position_array['display_name'] != "") && !$refreshed) {
			array_push($this->player_positions, $position_array);	

			//killed_by = 0 is 'no-one', so don't add to still playing array
			if ($killed_by != 0) {
				$this->players_playing[$killed_by]['heads_taken'][][$eliminated] = $this->players_playing[$eliminated]['display_name'];
			}
			if (!$rebuy && !$refreshed) {
				unset($this->players_playing[$eliminated]);
			}
		}


		$this->last_refresh_nonce = $refresh_nonce; //set last_refresh_nonce here to make sure we're doing it AFTER all the checks for refresh...

		// //force the action so we can redirect from add-results template
		// $_REQUEST['action'] = 'continue';
		echo "<pre style='font-size:x-small;'>";
		// echo "this->player_positions";
		print_r($this);
		// echo "position_array";
		// print_r($position_array);
		echo "</pre>";
		// $_SESSION['pkr_game_'.$this->event_id] = $this;		
		$this->game_in_progress();
		return;
	}

	public function finalise_results() {
		//all now in new class
		$positions = $this->player_positions;
		$total_pot = count($positions) * $this->buy_in_price;
		$finalise_error = NULL;
		$max_win = $total_pot;
		$total_winnings = 0;

		//check that position is first value in array before sorting
		$key_pos = array_search('position', array_keys($positions[0]));
		if ($key_pos == 0) {
			//sort by positions (as it is the first value)
			array_multisort($positions);
		}
		else {
			$finalise_error = 'positions_array';
			print_r("SYSTEM ERROR! Someone's broken the positions array!");
		}

		
		foreach ($positions as $position) {	
			$total_winnings = $total_winnings + $position['money_won'];
			$money_won = $position['money_won'];
			if ($money_won > $max_win) {
				$finalise_error = 'positions_mismatch';
				//hopefully gets position of the key that has the error...
				$position_error = key($positions);
				print_r('PE:'.$position_error);
			}
			$max_win = $position['money_won'];
		}

		if ($finalise_error) {
			echo "<h5>Error</h5>";
		}

		if ($finalise_error == 'positions_mismatch') {
			echo "<p>Positions and money won do not match.</p>";
		}

		if ($total_winnings != $total_pot) {
			$finalise_error = 'winnings_mismatch';
			echo "<p>Total winnings (&pound;".$total_winnings.") do not match total pot (&pound;".$total_pot."). </p><p> Please check each player's winnings.</p>";
		}

		//output results list
		echo "<ol>";

		foreach ($positions as $position) {
			$money_won = $position['money_won'];
			if (($money_won == $last_pos_won) && ($money_won > 0)) {
				$position['position'] = $position['position'] - 1;
			}

			echo "<li value=".$position['position'].">";
			echo $position['display_name'];
			echo "</li>";

			$last_pos_won = $money_won;
		}


		echo "</ol>";

		// echo $total_winnings;
		print_r("$positions:");
		echo "<pre>";
		print_r($positions);
		echo "</pre>";

		print_r("SESSION:");
		echo "<pre>";
		print_r($_SESSION);
		echo "</pre>";
	}


	public function show_player_confirm_list() {
		global $EM_Event;
		echo "<h4>Confirm players for: ".$EM_Event->event_name."</h4>";
		// $this->set_by[microtime()] = "show_player_confirm_list I";		

		// $this->set_by[microtime()] = "show_player_confirm_list II";
		$players_playing = $this->players_playing;
		$players_not_playing = $this->players_not_playing;
		// $this->players_array = $this->playing_and_not_yet_playing();
		// $players_array = $this->players_array;
		// echo "<pre>";
		// print_r($_SESSION);
		// print_r($this);
		// echo "</pre>";

		$tickets = $EM_Event->get_tickets()->tickets;
		if (count($tickets) > 1) {
			echo "There are more than one type of ticket available. Adding and removing WILL NOT work (yet).";
		}
		else {
			reset($tickets);
			$ticket_id = key($tickets);
			$buy_in = number_format($tickets[$ticket_id]->ticket_price, 2);
		}

		// print_r($ticket_price);

		?>
		<?php if (count($players_playing) > 1) {?>
		<form method="POST" class="start-game">
			<input type="hidden" name="buy_in" value="<?php echo $buy_in ?>" />				
			<input type="hidden" name="action" value="start_game" />
			<button type="submit" class="go big" style="font-size:initial; float:right; color: green; width:100%;">Start game with <strong><?php echo count($players_playing); ?></strong> players</button>
		</form>
		<?php
	} ?>
	<form method="POST" class="player-list">
		<table class="players">
			<?php if (is_integer($ticket_id)) { ?>
			<input type="hidden" name="ticket_id" value="<?php echo $ticket_id ?>" />
			<input type="hidden" name="buy_in" value="<?php echo $buy_in ?>" />				
			<input type="hidden" name="action" value="modify_list" />
			<?php
		}
		?>
			<thead>
				<tr>
					<th><?php _e('Currently registered players', 'pkr_stats'); ?></th>
					<th><?php _e('Add more players', 'pkr_stats'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<?php
						if( count($players_playing) > 0 ){
							?>
							<ul class="league-members attending" style="list-style:none;">
								<?php
								foreach( $players_playing as $player_id => $player){ 
									?>
									<li>
										<input type="checkbox" name="remove_booking[]" value="<?php echo $player['booking_id']; ?>" />
										<?php echo get_avatar($player_id, 25)
										.'&nbsp;'. 
										$player['display_name']
										.'&nbsp;'
										?>
									</li>
									<?php 
								}
								?>
							</ul>
							<?php
						}
						else {
							echo "No players";
						}
						?>
					</td>
					<td>
						<?php
						if( count($players_not_playing) > 0 ){
							?>
							<ul class="league-members not-attending" style="list-style:none;">
								<?php
								foreach( $players_not_playing as $player_id => $player){ 
									?>
									<li>
										<input type="checkbox" name="add_player[]" value="<?php echo $player_id; ?>" />
										<?php echo get_avatar($player_id, 25)
										.'&nbsp;'. 
										$player['display_name']
										.'&nbsp;'
										?>
									</li>
									<?php 
								}
								?>
							</ul>
							<?php
						}
						else {
							echo "No players";
						}
						?>						
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">
						<button type="submit">Add or remove players</button>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
	<form method="post" class="register-new">
		<table>
			<tr>
				<input type="hidden" name="ticket_id" value="<?php echo $ticket_id ?>" />
				<input type="hidden" name="buy_in" value="<?php echo $buy_in ?>" />
				<input type="hidden" name="action" value="register_and_book" />				
				<th>Register a new league member and book them on to this event</th>
			</tr>
			<tr>
				<td>
					<input type="text" name="user_name" placeholder="* Username" />
					<input type="text" name="first_name" placeholder="* First Name" />
					<input type="text" name="last_name" placeholder="* Surname" /
					>
					<input type="text" name="email" placeholder="* Email address" /
					>
					<p>An auto-generated password will be emailed to the new user.</p>
					<button type="submit">Register and book</button>


				</td>
			</tr>
		</table>

	</form>
<?php
}
}