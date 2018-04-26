<?php
include('db.php');
include('utils.php');
include('gamesutils.php');

if(!isset($_GET['bet']) || $_GET['bet'] == "") {
	$arr = array('status' => 'error', 'error' => 504, 'message' => 'Bet is not set.');
	echo json_encode($arr);
} else if($_GET['bet'] < 0.001) {
	$arr = array('status' => 'error', 'error' => 505, 'message' => 'Bet is too small.');
	echo json_encode($arr);
} else if(!IsLoggedOnUser()) {
	$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
	echo json_encode($arr);
} else {
	$query = $db->prepare('SELECT * FROM users WHERE username = ?');
	$query->bind_param('s', $_COOKIE['username']);
	
	$query->execute();
			
	$result = $query->get_result();
	if($result->num_rows) {
		while ($row = $result->fetch_assoc()) { 
			$balance = $row['balance'];
			$promobal = $row['promob'];
			$losted = $row['losted'];
			$won = $row['won'];
			$ref = $row['reffered'];
		}
		
		if(($promobal + $balance) >= $_GET['bet']) {
			
			$slot1 = slotPick();
			$slot2 = slotPick();
			$slot3 = slotPick();
			
			$win = slotIsWin($slot1, $slot2, $slot3);
			
			if(!$promobal) {
				$newbalance = $balance - $_GET['bet'];
			} else {
				if($promobal <= $_GET['bet']) {
					$betnew = $_GET['bet'] - $promobal;
					$promobal = 0;
					$newbalance = $balance - $betnew;
				} else {
					$promobal = $promobal - $_GET['bet'];
					$newbalance = $balance;
				}
			}
			
			if($win == 1) {
				$newbalance = $newbalance + ($_GET['bet'] * 2);
				$won += $_GET['bet'];
				$awon = $_GET['bet'];
				$winning = 1;
			}
			else if($win == 2) {
				$newbalance = $newbalance + ($_GET['bet'] * 2.5);
				$won += ($_GET['bet'] * 1.5);
				$awon = ($_GET['bet'] * 1.5);
				$winning = 1;
			}
			else if($win == 3) {
				$newbalance = $newbalance + ($_GET['bet'] * 3);
				$won += ($_GET['bet'] * 2);
				$awon = ($_GET['bet'] * 2);
				$winning = 1;
			}
			else if($win == 4) {
				$newbalance = $newbalance + ($_GET['bet'] * 4);
				$won += ($_GET['bet'] * 3);
				$awon = ($_GET['bet'] * 3);
				$winning = 1;
			}
			else if($win == 5) {
				$newbalance = $newbalance + ($_GET['bet'] * 5.5);
				$won += ($_GET['bet'] * 4.5);
				$awon = ($_GET['bet'] * 4.5);
				$winning = 1;
			}
			else if($win == 6) {
				$newbalance = $newbalance + ($_GET['bet'] * 7);
				$won += ($_GET['bet'] * 6);
				$awon = ($_GET['bet'] * 6);
				$winning = 1;
			}
			else {
				$losted += $_GET['bet'];
				$awon = 0;
				$winning = 0;
			}
			
			$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ?, promob = ? WHERE username = ?');
			$query->bind_param('dddds', $newbalance, $losted, $won, $promobal, $_COOKIE['username']);
				
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
			
			$transType = 9;
				
			$query = $db->prepare('INSERT INTO history (transType, amount, gameid, user1, win, reward, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?)');
			$query->bind_param('idisidi', $transType, $_GET['bet'], $game, $_COOKIE['username'], $winning, $awon, $timestampedd);
					
			$query->execute();
			
			$arr = array('status' => 'success', 'slot1' => $slot1, 'slot2' => $slot2, 'slot3' => $slot3, 'win' => $win, 'balance' => $newbalance);
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
?>