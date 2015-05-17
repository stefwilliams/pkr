<?php

class Pkr_Result{

	public $total_pot; //only variable not passed in from Pkr_Game.

	public function __construct($buy_in_price, $rebuy_flags, $total_rebuy_amount, $player_positions) {
		
		$this->buy_in_price = $buy_in_price;
		$this->rebuy_flags = $rebuy_flags;
		$this->total_rebuy_amount = $total_rebuy_amount;
		$this->player_positions = $player_positions;

		foreach ($this->player_positions as $position) {
			$total_pot += $position['money_lost'];
			# code...
		}
		$this->total_pot = $total_pot;
		// $this->total_pot = $this->buy_in_price * count($this->player_positions) + $this->total_rebuy_amount;

		$array_error = $this->check_positions_array();

		if (!$array_error) {
			$this->sort_positions_array();
		}

		$v_errors = $this->check_validation_errors();
		if ($v_errors) {
			echo "<h5>There are some errors in your results:</h5>";
			foreach ($v_errors as $error => $message) {
				echo $message;
			}
			$this->results_confirm_screen($editable = true);
		}

		else {
			$this->results_confirm_screen($editable = false);
		}
	}

	public function check_positions_array() {
		//check that the 'position' key is first in the positions array, as things will break if it's not
		$error = false;
		$player_positions = $this->player_positions;
		$key_pos = array_search('position', array_keys($this->player_positions[0]));

		if ($key_pos !== 0) {
			print_r("SERIOUS ERROR! A developer somewhere has broken the positions array!");
			exit;			
		}

	}

	public function sort_positions_array() {
		//sort player_positions array by player position (we've already checked that it is the first key)
		$positions = $this->player_positions;
		array_multisort($positions);
		unset($this->player_positions);
		$this->player_positions = $positions;
	}


	public function check_validation_errors() {
		$errors = array();

		$winnings_error = $this->check_winnings_vs_pot();
		if ($winnings_error) {
			$errors['winnings_error'] = $winnings_error;    
		}

		$winnings_mismatch = $this->check_winnings_mismatch();
		if ($winnings_mismatch) {
			$errors['winnings_mismatch'] = $winnings_mismatch;
		}
		return $errors;

	}

	public function check_winnings_vs_pot() {
		//check that total winnings matches total pot
		$error = false;
		$total_winnings = 0;
		foreach ($this->player_positions as $position) {
			$total_winnings += $position['money_won'];
		}
		if ($total_winnings != $this->total_pot) {
			$error = "<p>Total winnings (&pound;".$total_winnings.") do not match total pot (&pound;".$this->total_pot."). </br> Please check each player's winnings.</p>";
		}

		return $error;
	}


	public function check_winnings_mismatch() {
		//check that the amount won is equal or less as position decreases
		$error = false;
		$total_winnings = 0;
		$max_win = $this->total_pot;

		$position_errors = array();

		foreach ($this->player_positions as $position) {	
			$money_won = $position['money_won'];
			// print_r($position['position'].':'.$position['money_won']);
			if ($money_won > $max_win) {
				//hopefully gets position of the key that has the error...
				$pe = key($this->player_positions);
				array_push($position_errors, $pe);
				$error = true;
			}
			$max_win = $position['money_won'];
		}
		if ($error && !empty($position_errors)) {
			$error =  "<p>Positions and winnings do not match.";
			foreach ($position_errors as $key => $pe) {
				$error .= "<br />Position ".$pe." has won more money than a higher position.";
			}
			$error.= "</p>";
		}
		return $error;

	}

	public function results_confirm_screen($editable) {
		echo "<table><thead><tr>
		<th>Position</th>
		<th>Player</th>
		<th>Killer</th>
		<th>Rebuys</th>
		<th>&pound; lost</th>
		<th>&pound; won</th>

		</tr></thead>";

		foreach ($this->player_positions as $position) {

			$money_won = $position['money_won'];
			if (($money_won == $last_pos_won) && ($money_won > 0)) {
				$position['position'] = $position['position'] - 1;
			}

			echo "<tr><td>".$position['position']."</td>";
			echo "<td>".$position['display_name']."</td>";
			echo "<td>".$position['killer_name']."</td>";
			echo "<td>".$position['rebuy_amount']."</td>";
			echo "<td>".$position['money_lost']."</td>";
			echo "<td>".$position['money_won']."</td>";
			$last_pos_won = $money_won;
		}


		echo "</table>";
	}

}
?>