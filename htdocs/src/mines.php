<?php
include('utils.php');
include('gamesutils.php');
include('db.php');

if(!isset($_GET['action']) || $_GET['action'] == "") {
	$arr = array('status' => 'error', 'error' => 500, 'message' => 'Action is not set.');
	echo json_encode($arr);
} else if ($_GET['action'] == "newGame") {
	if(!isset($_GET['game'])) {
		$arr = array('status' => 'error', 'error' => 501, 'message' => 'Game is not set.');
		echo json_encode($arr);
	} else if (!IsLoggedOnUser()) {
		$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
		echo json_encode($arr);
	} else if($_GET['game'] != 0) {
		$arr = array('status' => 'error', 'error' => 503, 'message' => 'A game is already running. Please cash out to start another game.');
		echo json_encode($arr);
	} else if(!isset($_GET['bet'])) {
		$arr = array('status' => 'error', 'error' => 504, 'message' => 'Bet is not set.');
		echo json_encode($arr);
	} else if($_GET['bet'] < 0.001) {
		$arr = array('status' => 'error', 'error' => 505, 'message' => 'Bet is too small.');
		echo json_encode($arr);
	} else {
		$query = $db->prepare('SELECT * FROM users WHERE username = ?');
		$query->bind_param('s', $_COOKIE['username']);
	
		$query->execute();
			
		$result = $query->get_result();
		if($result->num_rows) {
			while ($row = $result->fetch_assoc()) { 
				$balance = $row['balance'];
			}
			
			if($balance >= $_GET['bet']) {
				$secret = generateSecretMines();
				$hash = hash("sha256", $secret);
				
				$mode = 1;
				
				$newbalance = $balance - $_GET['bet'];
				
				$query = $db->prepare('INSERT INTO mines (player, mode, secret, hash, bet, reward) VALUES (?, ?, ?, ?, ?, ?)');
				$query->bind_param('sissdd', $_COOKIE['username'], $mode, $secret, $hash, $_GET['bet'], $_GET['bet']);
				
				$query->execute();
				
				$game = mysqli_insert_id($db);
				
				$query = $db->prepare('UPDATE users SET balance = ? WHERE username = ?');
				$query->bind_param('ds', $newbalance, $_COOKIE['username']);
				
				$query->execute();
				
				$arr = array('status' => 'success', 'message' => 'Game has been successfully created.', 'game' => $game, 'hash' => $hash, 'reward' => $_GET['bet']);
				echo json_encode($arr);
			} else {
				$arr = array('status' => 'error', 'error' => 506, 'message' => 'You don\'t have enough money. Balance: '.$balance." SBD.");
				echo json_encode($arr);
			}
		} else {
			$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
			echo json_encode($arr);
		}
	}
} else if($_GET['action'] == "cashOut") {
	if(!isset($_GET['game'])) {
		$arr = array('status' => 'error', 'error' => 501, 'message' => 'Game is not set.');
		echo json_encode($arr);
	} else if (!IsLoggedOnUser()) {
		$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
		echo json_encode($arr);
	} else if($_GET['game'] == 0) {
		$arr = array('status' => 'error', 'error' => 507, 'message' => 'No game running. Please reload.');
		echo json_encode($arr);
	} else {
		$query = $db->prepare('SELECT * FROM mines WHERE id = ?');
		$query->bind_param('i', $_GET['game']);
	
		$query->execute();
			
		$result = $query->get_result();
		if($result->num_rows) {
			while ($row = $result->fetch_assoc()) { 
				$username = $row['player'];
				$reward = $row['reward'];
				$win = $row['win'];
				if($win == 0) {
					if($username == $_COOKIE['username']) {
						$query = $db->prepare('SELECT * FROM users WHERE username = ?');
						$query->bind_param('s', $_COOKIE['username']);
	
						$query->execute();
			
						$result = $query->get_result();
						if($result->num_rows) {
							while ($row = $result->fetch_assoc()) { 
								$balance = $row['balance'];
								$won = $row['won'];
							}
							$balance = $balance + $reward;
							$won = $won + $reward;
							
							$query = $db->prepare('UPDATE users SET balance = ?, won = ? WHERE username = ?');
							$query->bind_param('dds', $balance, $won, $_COOKIE['username']);
	
							$query->execute();
							
							$win = 1;
							
							$query = $db->prepare('UPDATE mines SET win = ? WHERE id = ?');
							$query->bind_param('ii', $win, $_GET['game']);
	
							$query->execute();
							
							$arr = array('status' => 'success', 'message' => 'You have won '.$reward.' SBD.');
							echo json_encode($arr);
						} else {
							$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
							echo json_encode($arr);
						}
					} else {
						$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
						echo json_encode($arr);
					}
				} else {
					$arr = array('status' => 'error', 'error' => 509, 'message' => 'Game has been already cashed out.');
					echo json_encode($arr);
				}
			}
		} else {
			$arr = array('status' => 'error', 'error' => 508, 'message' => 'Invalid gameID. Please reload.');
			echo json_encode($arr);
		}
	}
} else {
	$arr = array('status' => 'error', 'error' => 500, 'message' => 'Action is not set.');
	echo json_encode($arr);
}
?>