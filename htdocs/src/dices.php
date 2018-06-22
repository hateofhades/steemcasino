<?php
include('db.php');
include('utils.php');
include('gamesutils.php');

if(!isset($_GET['bet']) || $_GET['bet'] == "") {
	$arr = array('status' => 'error', 'error' => 504, 'message' => 'INVALID BET.');
	echo json_encode($arr);
} else if($_GET['bet'] < 0.001) {
	$arr = array('status' => 'error', 'error' => 505, 'message' => 'BET IS TOO SMALL.');
	echo json_encode($arr);
} else if(!isset($_GET['under']) || $_GET['under'] == "") {
	$arr = array('status' => 'error', 'error' => 444, 'message' => 'INVALID ROLL UNDER.');
	echo json_encode($arr);
} else if($_GET['under'] < 10 || $_GET['under'] > 9400) {
	$arr = array('status' => 'error', 'error' => 444, 'message' => 'INVALID ROLL UNDER.');
	echo json_encode($arr);
} else if(!IsLoggedOnUser()) {
	$arr = array('status' => 'error', 'error' => 502, 'message' => 'INVALID SESSION.');
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
			$dicessecret = $row['dicesecret'];
		}
		
		if(($promobal + $balance) >= $_GET['bet']) {
			
			if(!isset($_GET['secret']) || $_GET['bet'] == "")
				$playersecret = generateSecret();
			else {
				if(strlen($_GET['secret']) <= 32)
					$playersecret = $_GET['secret'];
				else
					$playersecret = substr($_GET['secret'], 0, 32);
			}
			
			$pick = dicesPick(substr($dicessecret, 0, 100), $playersecret);
			
			$multiplier = (9500/$_GET['under']);
			$multiplier = round($multiplier, 2);
			
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
			
			if($pick < $_GET['under']) {
				$win = 1;
				$newbalance = $newbalance + ($_GET['bet'] * $multiplier);
				$won += (($_GET['bet'] * $multiplier) - $_GET['bet']);
				$awon = (($_GET['bet'] * $multiplier) - $_GET['bet']);
			} else {
				$win = 0;
				$losted += $_GET['bet'];
				$awon = 0;
			}
			
			$newdicesecret = generateSecret()."-".generateSecret();
			$newdiceshash = hash("sha256", $newdicesecret);
			
			$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ?, promob = ?, dicesecret = ? WHERE username = ?');
			$query->bind_param('ddddss', $newbalance, $losted, $won, $promobal, $newdicesecret, $_COOKIE['username']);
				
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
			
			$transType = 10;
			
			
			$query = $db->prepare('INSERT INTO history (transType, amount, gameid, user1, win, reward, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?)');
			$query->bind_param('idisidi', $transType, $_GET['bet'], $game, $_COOKIE['username'], $win, $awon, $timestampedd);
					
			$query->execute();
			
			$arr = array('status' => 'success', 'win' => $win, 'secret' => $dicessecret, 'hash' => $newdiceshash, 'pick' => $pick, 'multiplier' => $multiplier, 'balance' => $newbalance, 'reward' => ($_GET['bet'] * $multiplier) - $_GET['bet'], 'under' => $_GET['under'], 'bet' => $_GET['bet']);
			echo json_encode($arr);
			
		} else {
			$arr = array('status' => 'error', 'error' => 506, 'message' => 'NOT ENOUGHT BALANCE.');
			echo json_encode($arr);
		}
	} else {
		$arr = array('status' => 'error', 'error' => 502, 'message' => 'SESSION IS INVALID.');
		echo json_encode($arr);
	}
}
?>