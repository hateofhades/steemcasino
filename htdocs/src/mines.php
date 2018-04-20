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
				$losted = $row['losted'];
				$ref = $row['reffered'];
			}
			
			if($balance >= $_GET['bet']) {
				$secret = generateSecretMines();
				$hash = hash("sha256", $secret);
				
				$mode = 1;
				
				$newbalance = $balance - $_GET['bet'];
				
				$losted = $losted + $_GET['bet'];
				
				$query = $db->prepare('INSERT INTO mines (player, mode, secret, hash, bet, reward) VALUES (?, ?, ?, ?, ?, ?)');
				$query->bind_param('sissdd', $_COOKIE['username'], $mode, $secret, $hash, $_GET['bet'], $_GET['bet']);
				
				$query->execute();
				
				$game = mysqli_insert_id($db);
				
				$query = $db->prepare('UPDATE users SET balance = ?, losted = ? WHERE username = ?');
				$query->bind_param('dds', $newbalance, $losted, $_COOKIE['username']);
				
				$query->execute();
				
				$timestampedd = time();
				
				if($ref) {
					$noyou = $db->prepare('SELECT * FROM users WHERE username = ?');
					$noyou->bind_param('s', $ref);
							
					$noyou->execute();
								
					$rt = $noyou->get_result();
					if($rt->num_rows) {
						while ($refrow = $rt->fetch_assoc()) {
							$refbalance = $refrow['balance'];
						}
					}
								
					$refbalance = $refbalance + ($_GET['bet']/1000);
				
					$noyou = $db->prepare('UPDATE users SET balance = ? WHERE username = ?');
					$noyou->bind_param('ds', $refbalance, $ref);
					$noyou->execute();
				
					$transType = 8;
					
					$refrew = $_GET['bet']/1000;
					
					$noyou = $db->prepare('INSERT INTO history (transType, user1, user2, reward, timestamp) VALUES (?, ?, ?, ?, ?)');
					$noyou->bind_param('issdi', $transType, $_COOKIE['username'], $ref, $refrew, $timestampedd);
					
					$noyou->execute();
				}
				
				$transType = 5;
				$winning = 0;
				
				$query = $db->prepare('INSERT INTO history (transType, amount, gameid, user1, win, timestamp) VALUES (?, ?, ?, ?, ?, ?)');
				$query->bind_param('idisii', $transType, $_GET['bet'], $game, $_COOKIE['username'], $winning, $timestampedd);
					
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
				$bet = $row['bet'];
				$secret = $row['secret'];
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
								$losted = $row['losted'];
							}
							$balance = $balance + $reward;
							$won = $won + $reward;
							$losted = $losted - $bet;
							
							$query = $db->prepare('UPDATE users SET balance = ?, won = ?, losted = ? WHERE username = ?');
							$query->bind_param('ddds', $balance, $won, $losted, $_COOKIE['username']);
	
							$query->execute();
							
							$win = 1;
							
							$query = $db->prepare('UPDATE mines SET win = ? WHERE id = ?');
							$query->bind_param('ii', $win, $_GET['game']);
	
							$query->execute();
							
							$query = $db->prepare('UPDATE history SET win = 1, reward = ? WHERE transType = 5 AND gameid = ? AND user1 = ?');
							$query->bind_param('dis', $reward, $_GET['game'], $_COOKIE['username']);
					
							$query->execute();
							
							$arr = array('status' => 'success', 'message' => 'You have won '.$reward.' SBD.', 'secret' => 'Secret: '.$secret);
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
					$arr = array('status' => 'error', 'error' => 509, 'message' => 'Game has already ended.');
					echo json_encode($arr);
				}
			}
		} else {
			$arr = array('status' => 'error', 'error' => 508, 'message' => 'Game does not exist.');
			echo json_encode($arr);
		}
	}
} else if($_GET['action'] == "hitBlock") {
	if(!isset($_GET['game'])) {
		$arr = array('status' => 'error', 'error' => 501, 'message' => 'Game is not set.');
		echo json_encode($arr);
	} else if (!IsLoggedOnUser()) {
		$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
		echo json_encode($arr);
	} else if($_GET['game'] == 0) {
		$arr = array('status' => 'error', 'error' => 507, 'message' => 'No game running. Please reload.');
		echo json_encode($arr);
	} else if(!isset($_GET['block'])) {
		$arr = array('status' => 'error', 'error' => 510, 'message' => 'Block is not set.');
		echo json_encode($arr);
	} else if($_GET['block'] > 25 || $_GET['block'] <= 0) {
		$arr = array('status' => 'error', 'error' => 511, 'message' => 'Block is invalid.');
		echo json_encode($arr);
	} else {
		$query = $db->prepare('SELECT * FROM mines WHERE id = ?');
		$query->bind_param('i', $_GET['game']);
	
		$query->execute();
			
		$result = $query->get_result();
		if($result->num_rows) {
			while ($row = $result->fetch_assoc()) { 
				$win = $row['win'];
				$bet = $row['bet'];
				$reward = $row['reward'];
				$secret = $row['secret'];
				$player = $row['player'];
				$blocks = $row['blocks'];
				
				$blocks = explode(" ", $blocks);
			} if ($win == 0) {
				if($player == $_COOKIE['username']) {
					$funded = 0;
					foreach($blocks as $blocked) {
						$blocked;
						if($blocked == $_GET['block'])
							$funded = 1;
					}
					
					if(!$funded) {
						$bombs = explode("-", $secret);
						array_pop($bombs);
						
						$found = 0;
						
						foreach($bombs as $bomb) {
							if($bomb == $_GET['block']) {
								$win = 2;
								
								$query = $db->prepare('UPDATE mines SET win = ? WHERE id = ?');
								$query->bind_param('ii', $win, $_GET['game']);
		
								$query->execute();
								
								$query = $db->prepare('UPDATE history SET win = 2, reward = 0 WHERE user1 = ? AND gameid = ? AND transType = 5');
								$query->bind_param('si', $_COOKIE['username'], $_GET['game']);
					
								$query->execute();
								
								$arr = array('status' => 'lost', 'message' => 'You hit a bomb. You lost.', 'secret' => ''.$secret, 'bombs' => $bombs);
								echo json_encode($arr);
								
								$found = 1;
								
								break;
							}
						}
						if($found == 0) {
							$plus = $reward / 100 * 13;
							$reward = $reward + $plus;
							
							$reward = number_format($reward, 5);
							$plus = number_format($plus, 5);
							
							array_push($blocks, $_GET['block']);
							
							$blocks = implode(" ", $blocks);
							
							$query = $db->prepare('UPDATE mines SET reward = ?, blocks = ? WHERE id = ?');
							$query->bind_param('dsi', $reward, $blocks, $_GET['game']);
							
							$query->execute();
								
							$arr = array('status' => 'increase', 'message' => 'You increased the reward to: '.$reward.' SBD.', 'block' => ''.$_GET['block'], 'increase' => ''.$plus);
							echo json_encode($arr);
								
						}
					} else {
						$arr = array('status' => 'error', 'error' => 511, 'message' => 'You have already hit this block.');
						echo json_encode($arr);
					}
				} else {
					$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
					echo json_encode($arr);
				}
			} else {
				$arr = array('status' => 'error', 'error' => 509, 'message' => 'Game has already ended.');
				echo json_encode($arr);
			}
		} else {
			$arr = array('status' => 'error', 'error' => 508, 'message' => 'Game does not exist.');
			echo json_encode($arr);
		}
	}
} else {
	$arr = array('status' => 'error', 'error' => 500, 'message' => 'Action is not set.');
	echo json_encode($arr);
}
?>