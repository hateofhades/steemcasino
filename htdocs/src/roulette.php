<?php
include('utils.php');
include('gamesutils.php');
include('db.php');

if(!isset($_GET['betOn']) || $_GET['betOn'] == "" || !isset($_GET['bet']) || $_GET['bet'] == "") {
	$arr = array('status' => 'error', 'error' => 600, 'message' => 'Invalid bet.');
	echo json_encode($arr);
} else if($_GET['betOn'] != 1 && $_GET['betOn'] != 2 && $_GET['betOn'] != 3 && ($_GET['betOn'] < 100 || $_GET['betOn'] > 137) && $_GET['betOn'] != 4 && $_GET['betOn'] != 5 && $_GET['betOn'] != 6 && $_GET['betOn'] != 7 && $_GET['betOn'] != 8) {
	$arr = array('status' => 'error', 'error' => 601, 'message' => 'Invalid bet on.');
	echo json_encode($arr);
} else if($_GET['bet'] < 0 || $_GET['bet'] < 0.001) {
	$arr = array('status' => 'error', 'error' => 602, 'message' => 'Invalid bet amount.');
	echo json_encode($arr);
} else if(!IsLoggedOnUser()) {
	$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
	echo json_encode($arr);
} else {
	$query = $db->prepare('SELECT * FROM info WHERE name = \'roulettestate\'');
	
	$query->execute();
	$result = $query->get_result();
	if($result->num_rows) {
		while ($row = $result->fetch_assoc()) { 
			if($row['value'] == 1) {
				$arr = array('status' => 'error', 'error' => 603, 'message' => 'You can\'t bet now.');
				echo json_encode($arr);
			} else {
				$queried = $db->prepare('SELECT * FROM info WHERE name = \'rouletteid\'');
				$queried->execute();
				$resultedd = $queried->get_result();
				if($resultedd->num_rows) {
					while ($rowedd = $resultedd->fetch_assoc()) {
						$gameidd = $rowedd['value'];
					}
				}
				
				$queryy = $db->prepare('SELECT * FROM users WHERE username = ?');
				$queryy->bind_param('s', $_COOKIE['username']);
	
				$queryy->execute();
				
				$rresult = $queryy->get_result();
				if($rresult->num_rows) {
					while ($rrow = $rresult->fetch_assoc()) { 
						$balance = $rrow['balance'];
						$losted = $rrow['losted'];
						$ref = $rrow['reffered'];
						$promobal = $rrow['promob'];
					}
					
					if(($promobal + $balance) < $_GET['bet']) {
						$arr = array('status' => 'error', 'error' => 604, 'message' => 'You do not have enough balance. Current balance: '.$balance.' SBD.');
						echo json_encode($arr);
					} else {
						$querw = $db->prepare('SELECT * FROM roulette WHERE player = ?');
						$querw->bind_param('s', $_COOKIE['username']);
	
						$querw->execute();
						
						$wesult = $querw->get_result();
						if($wesult->num_rows) {
							$arr = array('status' => 'error', 'error' => 605, 'message' => 'You can only bet once per round.');
							echo json_encode($arr);
						} else {
							$wwuery = $db->prepare('INSERT INTO roulette (player, bet, beton) VALUES (?, ?, ?)');
							$wwuery->bind_param('sdi', $_COOKIE['username'], $_GET['bet'], $_GET['betOn']);
	
							$wwuery->execute();
							
							$lostedd = $losted + $_GET['bet'];
							
							if(!$promobal) {
								$newbal = $balance - $_GET['bet'];
							} else {
								if($promobal <= $_GET['bet']) {
									$betnew = $_GET['bet'] - $promobal;
									$promobal = 0;
									$newbal = $balance - $betnew;
								} else {
									$promobal = $promobal - $_GET['bet'];
									$newbal = $balance;
								}
							}
							
							$noyou = $db->prepare('UPDATE users SET balance = ?, losted = ?, promob = ? WHERE username = ?');
							$noyou->bind_param('ddds', $newbal, $lostedd, $promobal, $_COOKIE['username']);
							
							$noyou->execute();
							
							$win = 2;
							
							$timestamped = time();
							
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
								$noyou->bind_param('issdi', $transType, $_COOKIE['username'], $ref, $refrew, $timestamped);
							
								$noyou->execute();
							}
							
							$transType = 6;
							
							$yesure = $db->prepare('INSERT INTO history (transType, amount, gameid, user1, win, timestamp) VALUES (?, ?, ?, ?, ?, ?)');
							$yesure->bind_param('idisii', $transType, $_GET['bet'], $gameidd, $_COOKIE['username'], $win, $timestamped);

							$yesure->execute();
							
							
							$arr = array('status' => 'success', 'message' => 'Your bet has been placed. Your new balance is: '.$newbal." SBD.", 'balance' => ($newbal + $promobal));
							echo json_encode($arr);
						}
					}
				}
			}
		}
	}
}
?>