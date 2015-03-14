<?php

class Pkr_Result {

	public $league_id;
	public $event_id;
	public $buy_in_price;
	public $in_progress = 0;
	public $is_complete = 0;
	public $rebuy_flags = 0;
	public $set_by;
	public $last_nonce;
	// public $all_league_players = array();
	public $players_playing = array();
	public $players_not_playing = array();
	public $player_positions = array(
		// 'position' => '',
		// 'display_name' => '',
		// 'user_id' => '',
		// 'killer_name' => '',
		// 'killer_id' => '',
		// 'rebuy' => 0,
		// 'money_won' => 0,
		// 'money_lost' => '',
		// 'comment' => '',
		);

	public function __construct($event_id) {
		if ($_REQUEST['action'] == 'reset_game') {
			$event_id = $_REQUEST['event_id'];
			unset($this->set_by);
			unset($this->in_progress);
			unset($this->rebuy_flags);
			unset($this->players_playing);
			unset($this->players_not_playing);
			unset($this->player_positions);
		}
		$all_players = array();
		$all_players = $this->pkr_all_players();

		$this->league_id = bp_get_group_id();
		$this->event_id = $_REQUEST['event_id'];
		$this->players_playing = $all_players['players_playing'];
		$this->players_not_playing = $all_players['players_not_playing'];
		$this->set_by[time()] = "construct";
		
		// $_SESSION['pkr_game_'.$this->event_id] = $this;
	}

//puts all currently booked members into 'players_playing' and all other league members into 'players_not_playing'

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
		$ticket_price = $_POST['ticket_price'];
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

			$this->set_by[time()] = "pkr_em_delete_single_booking";

			$_SESSION['pkr_game_'.$this->event_id] = $this;
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

			$this->set_by[time()] = "pkr_em_add_single_booking";		

			$_SESSION['pkr_game_'.$this->event_id] = $this;
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
		$ticket_price = $_POST['ticket_price'];

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
				return;
			}
			else {
				echo "Something might have gone wrong...";
				return;
			}			
		}
		else {
			echo $user_id->get_error_message();
			return;
		}
	}

	public function game_in_progress() {
		$this->in_progress = 1;
		$this->set_by[time()] = "game_in_progress";
// print_r($_POST);
		$this->buy_in_price = $_POST['ticket_price'];		
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
				echo '<li>'.get_avatar($player_id, 25).'<a style="border:none;" href="?action=eliminate&amp;player_id='.$player_id.'&amp;event_id='.$this->event_id.'&amp;ticket_price='.$this->buy_in_price.'">'
				.'&nbsp;'. 
				$player['display_name']
				.'&nbsp;</a>';
				echo "<br /><span style='font-size:small;'>Heads: ";
				if (count($player['heads_taken']) > 0) {
					echo count($player['heads_taken']).'&nbsp;';
					foreach ($player['heads_taken'] as $defeated_id => $defeated_player) {
						echo get_avatar($defeated_id, 15).'&nbsp;';
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

		echo "<div style='width:100%;clear:both;text-align:center;'><a href='?action=reset_game&amp;event_id=".$this->event_id."'>Reset game</a></div>";
		$_SESSION['pkr_game_'.$this->event_id] = $this;
		

		return;
	}

	public function eliminate() {
		// echo "<pre style='font-size:x-small;'>";
		// echo "eliminate (incoming)<br />";
		// echo "this<br />";
		// print_r($this);
		// echo "SESSION<br />";
		// print_r($_SESSION);
		// echo "</pre>";
		$this->in_progress = 1;
		$this->set_by[time()] = "eliminate";
		// echo "<pre style='font-size:x-small;'>";
		// print_r($this);
		// print_r($_REQUEST);
		// echo "</pre>";

		$eliminated = $_REQUEST['player_id'];
		$buy_in_price = $_REQUEST['ticket_price'];
		// print_r($eliminated);
		//remove the person just eliminated

		$remaining_players = $this->players_playing;
		unset($remaining_players[$eliminated]);
		// echo "<pre style='font-size:x-small;'>";
		// // print_r($players);
		// print_r($remaining_players);
		// echo "</pre>";
		?>
		<form method="post" class="eliminate">
			<?php
			$nonce = wp_create_nonce( 'eliminate'.$eliminated );
			// $this->last_nonce = $nonce;
			?>

			<input type="hidden" name="nonce" value="<?php echo $nonce; ?>">
			<input type="hidden" name="action" value="eliminated">
			<input type="hidden" name="player_id" value="<?php echo $eliminated ?>">
			<input type="hidden" name="ticket_price" value="<?php echo $buy_in_price ?>">
			<p>
				<label for="eliminated_by">Head taken by:</label>
				<select name="eliminated_by">
					<option value="0">--no-one</option>
					<?foreach ($remaining_players as $user_id => $userdata) {?>
					<option value="<?php echo $user_id;?>"><?php echo $userdata['display_name'];?></option>				
					<?php } ?>
				</select>
			</p>
			<p>
				<label for="rebuy">Rebuy?: </label>
				<input type="checkbox" name="rebuy" />
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
		// echo "<pre style='font-size:x-small;'>";
		// echo "eliminate (before set new session)<br />";
		// echo "this<br />";
		// print_r($this);
		// echo "SESSION<br />";
		// print_r($_SESSION);
		// echo "</pre>";		
		$_SESSION['pkr_game_'.$this->event_id] = $this;		

		return;
	}

	public function eliminated() {
		$nonce = $_REQUEST['nonce'];
		$last_nonce = $this->last_nonce;

		if ($nonce == $last_nonce) {
			// print_r('refresh detected - ignore');
			$refreshed = true;
		}
		
		$eliminated = $_REQUEST['player_id'];
		$killed_by = $_REQUEST['eliminated_by'];
		if ($_REQUEST['rebuy']) {
			$rebuy = 1;
			$rebuys_to_date = $this->rebuy_flags;
			$this->rebuy_flags = $rebuys_to_date + $rebuy;
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
		$money_lost = $_REQUEST['ticket_price'];

		$this->in_progress = 1;
		$this->buy_in_price = $money_lost;
		$this->set_by[time()] = "eliminated";

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
			'money_won' => $money_won,
			'money_lost' => $money_lost,
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
				$this->players_playing[$killed_by]['heads_taken'][$eliminated] = $this->players_playing[$eliminated]['display_name'];
			}
			if (!$rebuy && !$refreshed) {
				unset($this->players_playing[$eliminated]);
			}
		}


		$this->last_nonce = $nonce; //set last_nonce here to make sure we're doing it AFTER all the checks for refresh...

		// //force the action so we can redirect from add-results template
		// $_REQUEST['action'] = 'continue';

		$_SESSION['pkr_game_'.$this->event_id] = $this;		
		$this->game_in_progress();
		return;
	}


	public function show_player_confirm_list() {
		global $EM_Event;
		$this->set_by[time()] = "show_player_confirm_list I";		
		if ($_REQUEST['action'] == 'modify_list') {
			$this->modify_player_list();
			return;
			// $players_array = $this->playing_and_not_yet_playing();
		}
		if ($_REQUEST['action'] == 'reset_game') {
			$this->__construct($event_id);
			return;
		}
		if ($_REQUEST['action'] == 'register_and_book') {
			$this->register_and_book();
			return;
		}
		if ($_REQUEST['action'] == 'start_game') {
			$this->game_in_progress();
			return;
		}
		if ($_REQUEST['action'] == "eliminated") {
			$this->eliminated();
			return;
		}
		$this->set_by[time()] = "show_player_confirm_list II";
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
			$ticket_price = $tickets[$ticket_id]->ticket_price;
		}

		$this->buy_in_price = $ticket_price;
		// print_r($ticket_price);

		?>
		<?php if (count($players_playing) > 1) {?>
		<form method="POST" class="start-game">
			<input type="hidden" name="ticket_price" value="<?php echo $ticket_price ?>" />				
			<input type="hidden" name="action" value="start_game" />
			<button type="submit" class="go big" style="font-size:initial; float:right; color: green; width:100%;">Start game with <strong><?php echo count($players_playing); ?></strong> players</button>
		</form>
		<?php
	} ?>
	<form method="POST" class="player-list">
		<table class="players">
			<?php if (is_integer($ticket_id)) { ?>
			<input type="hidden" name="ticket_id" value="<?php echo $ticket_id ?>" />
			<input type="hidden" name="ticket_price" value="<?php echo $ticket_price ?>" />				
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
			<input type="hidden" name="ticket_price" value="<?php echo $ticket_price ?>" />
			<input type="hidden" name="action" value="register_and_book" />				
			<th>Register and book a new league member</th>
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
?>