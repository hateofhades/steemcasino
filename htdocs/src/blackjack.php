<?php
include('utils.php');
include('gamesutils.php');
include('db.php');

if(!IsLoggedOnUser()) {
	$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
	echo json_encode($arr);
} else if(isset($_GET['action']) && $_GET['action'] != "") {
	if($_GET['action'] == "new") {
		if(!isset($_GET['bet']) || $_GET['bet'] == "") {
			$arr = array('status' => 'error', 'error' => 600, 'message' => 'Invalid bet.');
			echo json_encode($arr);
		} else if($_GET['bet'] < 0.001) {
			$arr = array('status' => 'error', 'error' => 602, 'message' => 'Invalid bet amount.');
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
					$ref = $row['reffered'];
				}
				if(($promobal + $balance) >= $_GET['bet']) {
					$deck = createDeck();
					
					$playerDraw = drawCards($deck, 2);
					$deck = removeCards($deck, 2);
					
					$houseDraw = drawCards($deck, 2);
					$deck = removeCards($deck, 2);
					
					$playerDrawString = json_encode($playerDraw);
					$deckString = json_encode($deck);
					$houseDrawString = json_encode($houseDraw);
					
					if((($playerDraw[0][0] == 11 && ($playerDraw[1][0] > 11 || $playerDraw[1][0] == 10)) || ($playerDraw[1][0] == 11 && ($playerDraw[0][0] > 11 || $playerDraw[0][0] == 10))) && (($houseDraw[0][0] != 11 || ($houseDraw[1][0] <= 11 && $houseDraw[1][0] != 10)) && ($houseDraw[1][0] != 11 || ($houseDraw[0][0] < 11 && $houseDraw[0][0] != 10))))
					{
						$isBlackjack = 1;
						
						$winnn = 1;
						
						$query = $db->prepare('INSERT INTO blackjack (player, bet, deck, playerHand, houseHand, win) VALUES (?, ?, ?, ?, ?, ?)');
						$query->bind_param('sdsssi', $_COOKIE['username'], $_GET['bet'], $deckString, $playerDrawString, $houseDrawString, $winnn);
				
						$query->execute();
					
						$game = mysqli_insert_id($db);
						
						if(!$promobal) {
							$newbalance = $balance + $_GET['bet'];
						} else {
							if($promobal <= $_GET['bet']) {
								$newbalance = $balance + $promobal + $_GET['bet'];
								$promobal = 0;
							} else {
								$promobal = $promobal - $_GET['bet'];
								$newbalance = $balance + $_GET['bet'] + $_GET['bet'];
							}
						}
					} else if((($playerDraw[0][0] == 11 && ($playerDraw[1][0] > 11 || $playerDraw[1][0] == 10)) || ($playerDraw[1][0] == 11 && ($playerDraw[0][0] > 11 || $playerDraw[0][0] == 10))) && (($houseDraw[0][0] == 11 && ($houseDraw[1][0] > 11 || $houseDraw[1][0] == 10)) || ($houseDraw[1][0] == 11 && ($houseDraw[0][0] > 11 || $houseDraw[0][0] == 10)))) {
						$isBlackjack = 2;
						
						$winnn = 3;
						
						$query = $db->prepare('INSERT INTO blackjack (player, bet, deck, playerHand, houseHand, win) VALUES (?, ?, ?, ?, ?, ?)');
						$query->bind_param('sdsssi', $_COOKIE['username'], $_GET['bet'], $deckString, $playerDrawString, $houseDrawString, $winnn);
				
						$query->execute();
					
						$game = mysqli_insert_id($db);
						
						if(!$promobal) {
							$newbalance = $balance;
						} else {
							if($promobal <= $_GET['bet']) {
								$newbalance = $balance + $promobal;
								$promobal = 0;
							} else {
								$promobal = $promobal - $_GET['bet'];
								$newbalance = $balance + $_GET['bet'];
							}
						}
					} else {
						$isBlackjack = 0;
						$losted = $losted + $_GET['bet'];
						
						$winnn = 0;
						
						$query = $db->prepare('INSERT INTO blackjack (player, bet, deck, playerHand, houseHand) VALUES (?, ?, ?, ?, ?)');
						$query->bind_param('sdsss', $_COOKIE['username'], $_GET['bet'], $deckString, $playerDrawString, $houseDrawString);
				
						$query->execute();
					
						$game = mysqli_insert_id($db);
						
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
					}
					
					$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, promob = ? WHERE username = ?');
					$query->bind_param('ddds', $newbalance, $losted, $promobal, $_COOKIE['username']);
				
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
				
				$transType = 11;
				
				$query = $db->prepare('INSERT INTO history (transType, amount, gameid, user1, win, timestamp) VALUES (?, ?, ?, ?, ?, ?)');
				$query->bind_param('idisii', $transType, $_GET['bet'], $game, $_COOKIE['username'], $winnn, $timestampedd);
					
				$query->execute();
				
				$arr = array('status' => 'success', 'message' => 'Game has been successfully created.', 'game' => $game, 'playerDraw' => $playerDraw, 'houseDraw' => [$houseDraw[0]], 'balance' => $newbalance, 'blackjack' => $isBlackjack, 'points' => checkPoints($playerDraw));
				echo json_encode($arr);
					
				} else {
					$arr = array('status' => 'error', 'error' => 812, 'message' => 'You do not have enough money. Balance: '.($balance+$promobal).' SBD');
					echo json_encode($arr);
				}
			} else {
				$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
				echo json_encode($arr);
			}
		}
	} else if($_GET['action'] == "hit") {
		if(!isset($_GET['game']) || $_GET['game'] == "" || $_GET['game'] == 0) {
			$arr = array('status' => 'error', 'error' => 501, 'message' => 'Game is not set.');
			echo json_encode($arr);
		} else {
			$query = $db->prepare('SELECT * FROM blackjack WHERE id = ?');
			$query->bind_param('i', $_GET['game']);
		
			$query->execute();
				
			$result = $query->get_result();
			if($result->num_rows) {
				while ($row = $result->fetch_assoc()) { 
				$win = $row['win'];
				$bet = $row['bet'];
				$deckString = $row['deck'];
				$playerHandString = $row['playerHand'];
				$houseHandString = $row['houseHand'];
				$state = $row['state'];
				$player = $row['player'];
				
				$deck = json_decode($deckString);
				$playerHand = json_decode($playerHandString);
				$houseHand = json_decode($houseHandString);
				
				}
				
				if($win == 0) {
					if($player == $_COOKIE['username']) {
						
						$playerHand = drawCards($deck, 1, $playerHand);
						
						$cardDraw = drawCards($deck);
						$deck = removeCards($deck);
						
						$deckString = json_encode($deck);
						$playerHandString = json_encode($playerHand);
						
						if(checkPoints($playerHand) > 21)
							$win = 2;
						else
							$win = 0;
						
						if(checkPoints($playerHand) != 21) {
						$query = $db->prepare('UPDATE blackjack SET deck = ?, playerHand = ?, win = ? WHERE ID = ?');
						$query->bind_param('ssii', $deckString, $playerHandString, $win, $_GET['game']);
				
						$query->execute();
						}
						
						$arr = array('status' => 'success', 'message' => 'Card successfully drawn.', 'card' => $cardDraw, 'points' => checkPoints($playerHand), 'win' => $win);
						echo json_encode($arr);
						
					} else {
						$arr = array('status' => 'error', 'error' => 981, 'message' => 'This is not your game.');
						echo json_encode($arr);
					}
				} else {
					$arr = array('status' => 'error', 'error' => 982, 'message' => 'This game has already finished.');
					echo json_encode($arr);
				}
				
			} else {
				$arr = array('status' => 'error', 'error' => 501, 'message' => 'Game does not exist.');
				echo json_encode($arr);
			}
		}
	} else if($_GET['action'] == "stand") {
		if(!isset($_GET['game']) || $_GET['game'] == "" || $_GET['game'] == 0) {
			$arr = array('status' => 'error', 'error' => 501, 'message' => 'Game is not set.');
			echo json_encode($arr);
		}
	} else if($_GET['action'] == "split") { 
		if(!isset($_GET['game']) || $_GET['game'] == "" || $_GET['game'] == 0) {
				$arr = array('status' => 'error', 'error' => 501, 'message' => 'Game is not set.');
				echo json_encode($arr);
			}
	} else if($_GET['action'] == "double") { 
		if(!isset($_GET['game']) || $_GET['game'] == "" || $_GET['game'] == 0) {
				$arr = array('status' => 'error', 'error' => 501, 'message' => 'Game is not set.');
				echo json_encode($arr);
			}
	} else if($_GET['action'] == "surrender") { 
		if(!isset($_GET['game']) || $_GET['game'] == "" || $_GET['game'] == 0) {
			$arr = array('status' => 'error', 'error' => 501, 'message' => 'Game is not set.');
			echo json_encode($arr);
		}
	} else if($_GET['action'] == "insurance") { 
		if(!isset($_GET['game']) || $_GET['game'] == "" || $_GET['game'] == 0) {
			$arr = array('status' => 'error', 'error' => 501, 'message' => 'Game is not set.');
			echo json_encode($arr);
		}
	} else {
		$arr = array('status' => 'error', 'error' => 802, 'message' => 'Action is invalid.');
		echo json_encode($arr);
	}
} else {
	$arr = array('status' => 'error', 'error' => 802, 'message' => 'Action is invalid.');
	echo json_encode($arr);
}
?>