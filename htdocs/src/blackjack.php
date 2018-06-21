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
					$won = $row['won'];
					$ref = $row['reffered'];
				}
				if(($promobal + $balance) >= $_GET['bet']) {
					$seed = mt_rand();
					
					$secret = $seed."-".generateSecret();
					$hash = hash("sha256", $secret);
					
					$deck = createDeck($seed);
					
					$playerDraw = drawCards($deck, 2);
					$deck = removeCards($deck, 2);
					
					$houseDraw = drawCards($deck, 2);
					$deck = removeCards($deck, 2);
					
					$ssecret = "";
					
					$playerDrawString = json_encode($playerDraw);
					$deckString = json_encode($deck);
					$houseDrawString = json_encode($houseDraw);
					
					$playerPoints = checkPoints($playerDraw);
					$housePoints = checkPoints($houseDraw);
					
					//We check to see if the player or the house has blackjack and if so we end the game, else we continue with it.
					
					if($playerPoints == 21 && $housePoints != 21)
					{
						$isBlackjack = 1;
						$ssecret = $secret;
						$winnn = 1;
						
						if(!$promobal) {
							$newbalance = $balance + ($_GET['bet'] * 1.5);
						} else {
							if($promobal <= $_GET['bet']) {
								$newbalance = $balance + $promobal + ($_GET['bet'] * 1.5);
								$promobal = 0;
							} else {
								$promobal = $promobal - $_GET['bet'];
								$newbalance = $balance + $_GET['bet'] + ($_GET['bet'] * 1.5);
							}
						}
						
						$won += ($_GET['bet'] * 1.5);
						$losted -= $_GET['bet'];
						
					} else if($playerPoints == 21 && $housePoints == 21) {
						$isBlackjack = 2;
						
						$ssecret = $secret;
						
						$winnn = 3;
						
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
						
						$losted -= $bet;
					} else if($housePoints == 21) {
						$isBlackjack = 3;
						
						$ssecret = $secret;
						
						$winnn = 2;
						
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
						
						$losted = $losted + $_GET['bet'];
					} else {
						$isBlackjack = 0;
						$losted = $losted + $_GET['bet'];
						$winnn = 0;
						
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
					
					$query = $db->prepare('INSERT INTO blackjack (player, bet, deck, playerHand, houseHand, win, secret, hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
					$query->bind_param('sdsssiss', $_COOKIE['username'], $_GET['bet'], $deckString, $playerDrawString, $houseDrawString, $winnn, $secret, $hash);
				
					$query->execute();
					
					$game = mysqli_insert_id($db);
					
					$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, promob = ?, won = ? WHERE username = ?');
					$query->bind_param('dddds', $newbalance, $losted, $promobal, $won, $_COOKIE['username']);
				
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
				
				if($isBlackjack)
					$housed = $houseDraw;
				else
					$housed = [$houseDraw[0]];
				
				if($houseDraw[0][0] == 11)
					$insurance = 1;
				else
					$insurance = 0;
				
				$arr = array('status' => 'success', 'secret' => $ssecret, 'message' => 'Game has been successfully created.', 'hash' => $hash, 'game' => $game, 'insurance' => $insurance, 'playerDraw' => $playerDraw, 'houseDraw' => $housed, 'balance' => $newbalance, 'blackjack' => $isBlackjack, 'points' => checkPoints($playerDraw));
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
				$insurance = $row['insurance'];
				$secret = $row['secret'];
				
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
						
						if(checkPoints($playerHand) > 21) {
							$win = 2;
							$house = $houseHand;
							$ssecret = $secret;
						}
						else {
							$win = 0;
							$house = [];
							$ssecret = "";
						}
						
						if(checkPoints($playerHand) != 21) {
						$state = 1;
						$query = $db->prepare('UPDATE blackjack SET deck = ?, playerHand = ?, win = ?, state = ? WHERE ID = ?');
						$query->bind_param('ssiii', $deckString, $playerHandString, $win, $state, $_GET['game']);
				
						$query->execute();
						
						$arr = array('status' => 'success', 'secret' => $ssecret, 'message' => '5', 'message' => '1', 'card' => $cardDraw, 'housePoints' => '0', 'playerHand' => $playerHand, 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $house);
						echo json_encode($arr, JSON_NUMERIC_CHECK);
						
						} else {
							if(checkPoints($houseHand) == 21) {
								$win = 2;
								
								$query = $db->prepare('UPDATE blackjack SET deck = ?, playerHand = ?, win = ? WHERE ID = ?');
								$query->bind_param('ssii', $deckString, $playerHandString, $win, $_GET['game']);
				
								$query->execute();
								
								if($insurance) {
									$query = $db->prepare('SELECT * FROM users WHERE username = ?');
									$query->bind_param('s', $_COOKIE['username']);
								
									$query->execute();
									
									$result = $query->get_result();
									while ($row = $result->fetch_assoc()) { 
										$balance = $row['balance'];
										$losted = $row['losted'];
										$won = $row['won'];
									}
									
									$balance += $bet;
									$losted -= $bet;
									
									$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
									$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
							
									$query->execute();
								}
								
								$arr = array('status' => 'success', 'secret' => $secret, 'message' => '5', 'card' => $cardDraw, 'housePoints' => checkPoints($houseHand), 'playerHand' => $playerHand, 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $houseHand);
								echo json_encode($arr, JSON_NUMERIC_CHECK);
							} else if(checkPoints($houseHand) >= 17) {
								$win = 1;
								
								$query = $db->prepare('UPDATE blackjack SET deck = ?, playerHand = ?, win = ? WHERE ID = ?');
								$query->bind_param('ssii', $deckString, $playerHandString, $win, $_GET['game']);
				
								$query->execute();
								
								$query = $db->prepare('SELECT * FROM users WHERE username = ?');
								$query->bind_param('s', $_COOKIE['username']);
							
								$query->execute();
								
								$result = $query->get_result();
								while ($row = $result->fetch_assoc()) { 
									$balance = $row['balance'];
									$losted = $row['losted'];
									$won = $row['won'];
								}
								
								$balance += ($bet + $bet);
								$won += $bet;
								$losted -= $bet;
												
								$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
								$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
							
								$query->execute();
								
								$query = $db->prepare('UPDATE history SET win = ?, reward = ? WHERE transType = ? AND gameid = ?');
								$query->bind_param('idii', $win, $bet, $trans, $_GET['game']);
								
								$query->execute();
								
								$arr = array('status' => 'success', 'secret' => $secret, 'message' => '3', 'card' => $cardDraw, 'playerHand' => $playerHand, 'housePoints' => checkPoints($houseHand), 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $houseHand);
								echo json_encode($arr, JSON_NUMERIC_CHECK);
							} else {
								$houseHand = drawHouse($houseHand, $deck);
								$houseHandString = json_encode($houseHand);
								
								if(checkPoints($houseHand) == 21) {
									$win = 3;
								
									$query = $db->prepare('UPDATE blackjack SET deck = ?, playerHand = ?, $houseHand = ?, win = ? WHERE ID = ?');
									$query->bind_param('sssii', $deckString, $playerHandString, $houseHand, $win, $_GET['game']);
					
									$query->execute();
									
									$query = $db->prepare('SELECT * FROM users WHERE username = ?');
									$query->bind_param('s', $_COOKIE['username']);
								
									$query->execute();
									
									$result = $query->get_result();
									while ($row = $result->fetch_assoc()) { 
										$balance = $row['balance'];
										$losted = $row['losted'];
										$won = $row['won'];
									}
									
									$balance += $bet;
									$losted -= $bet;
													
									$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
									$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
								
									$query->execute();
									
									$query = $db->prepare('UPDATE history SET win = ?, reward = ? WHERE transType = ? AND gameid = ?');
									$query->bind_param('idii', $win, $bet, $trans, $_GET['game']);
									
									$query->execute();
									
									$arr = array('status' => 'success', 'secret' => $secret, 'message' => '4', 'card' => $cardDraw, 'housePoints' => checkPoints($houseHand), 'playerHand' => $playerHand, 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $houseHand);
									echo json_encode($arr, JSON_NUMERIC_CHECK);
								} else if(checkPoints($houseHand) > 21 || checkPoints($playerHand) > checkPoints($houseHand)) {
									$win = 1;
								
									$query = $db->prepare('UPDATE blackjack SET deck = ?, playerHand = ?, win = ? WHERE ID = ?');
									$query->bind_param('ssii', $deckString, $playerHandString, $win, $_GET['game']);
					
									$query->execute();
									
									$query = $db->prepare('SELECT * FROM users WHERE username = ?');
									$query->bind_param('s', $_COOKIE['username']);
								
									$query->execute();
									
									$result = $query->get_result();
									while ($row = $result->fetch_assoc()) { 
										$balance = $row['balance'];
										$losted = $row['losted'];
										$won = $row['won'];
									}
									
									$balance += ($bet + $bet);
									$won += $bet;
									$losted -= $bet;
													
									$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
									$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
								
									$query->execute();
									
									$query = $db->prepare('UPDATE history SET win = ?, reward = ? WHERE transType = ? AND gameid = ?');
									$query->bind_param('idii', $win, $bet, $trans, $_GET['game']);
									
									$query->execute();
									
									$arr = array('status' => 'success', 'secret' => $secret, 'message' => '3', 'card' => $cardDraw, 'housePoints' => checkPoints($houseHand), 'playerHand' => $playerHand, 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $houseHand);
									echo json_encode($arr, JSON_NUMERIC_CHECK);
								} else {
									$win = 2;
								
									$query = $db->prepare('UPDATE blackjack SET deck = ?, playerHand = ?, win = ? WHERE ID = ?');
									$query->bind_param('ssii', $deckString, $playerHandString, $win, $_GET['game']);
				
									$query->execute();
								
									$arr = array('status' => 'success', 'secret' => $secret, 'message' => '2', 'card' => $cardDraw, 'housePoints' => checkPoints($houseHand), 'playerHand' => $playerHand, 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $houseHand);
									echo json_encode($arr, JSON_NUMERIC_CHECK);
								}
							}
						}
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
				$insurance = $row['insurance'];
				$player = $row['player'];
				$secret = $row['secret'];
				
				$deck = json_decode($deckString);
				$playerHand = json_decode($playerHandString);
				$houseHand = json_decode($houseHandString);
				
				}
				
				if($win == 0) {
					if($player == $_COOKIE['username']) {
						if(checkPoints($houseHand) >= 17) {
							if(checkPoints($houseHand) > checkPoints($playerHand)) {
								$win = 2;
								
								$query = $db->prepare('UPDATE blackjack SET win = ? WHERE ID = ?');
								$query->bind_param('ii', $win, $_GET['game']);
				
								$query->execute();
								
								if(checkPoints($houseHand) == 21) {
									if($insurance) {
										$query = $db->prepare('SELECT * FROM users WHERE username = ?');
										$query->bind_param('s', $_COOKIE['username']);
									
										$query->execute();
										
										$result = $query->get_result();
										while ($row = $result->fetch_assoc()) { 
											$balance = $row['balance'];
											$losted = $row['losted'];
											$won = $row['won'];
										}
										
										$balance += $bet;
										$losted -= $bet;
										
										$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
										$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
								
										$query->execute();
									}
								}
								
								$arr = array('status' => 'success', 'secret' => $secret, 'message' => 'House had more points than you.', 'housePoints' => checkPoints($houseHand), 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $houseHand);
								echo json_encode($arr);
							} else if(checkPoints($houseHand) == checkPoints($playerHand)) {
								$win = 3;
								
								$query = $db->prepare('UPDATE blackjack SET win = ? WHERE ID = ?');
								$query->bind_param('ii', $win, $_GET['game']);
					
								$query->execute();
									
								$query = $db->prepare('SELECT * FROM users WHERE username = ?');
								$query->bind_param('s', $_COOKIE['username']);
								
								$query->execute();
									
								$result = $query->get_result();
								while ($row = $result->fetch_assoc()) { 
									$balance = $row['balance'];
									$losted = $row['losted'];
									$won = $row['won'];
								}
									
								$balance += $bet;
								$losted -= $bet;
													
								$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
								$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
								
								$query->execute();
									
								$query = $db->prepare('UPDATE history SET win = ?, reward = ? WHERE transType = ? AND gameid = ?');
								$query->bind_param('idii', $win, $bet, $trans, $_GET['game']);
									
								$query->execute();
									
								$arr = array('status' => 'success', 'secret' => $secret, 'message' => 'Draw', 'housePoints' => checkPoints($houseHand), 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $houseHand);
								echo json_encode($arr);
							} else {
								$win = 1;
								
								$query = $db->prepare('UPDATE blackjack SET win = ? WHERE ID = ?');
								$query->bind_param('ii', $win, $_GET['game']);
					
								$query->execute();
									
								$query = $db->prepare('SELECT * FROM users WHERE username = ?');
								$query->bind_param('s', $_COOKIE['username']);
								
								$query->execute();
									
								$result = $query->get_result();
								while ($row = $result->fetch_assoc()) { 
									$balance = $row['balance'];
									$losted = $row['losted'];
									$won = $row['won'];
								}
									
								$balance += ($bet + $bet);
								$won += $bet;
								$losted -= $bet;
													
								$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
								$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
								
								$query->execute();
								
								$query = $db->prepare('UPDATE history SET win = ?, reward = ? WHERE transType = ? AND gameid = ?');
								$query->bind_param('idii', $win, $bet, $trans, $_GET['game']);
									
								$query->execute();
									
								$arr = array('status' => 'success', 'secret' => $secret, 'message' => 'You have won.', 'housePoints' => checkPoints($houseHand), 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $houseHand);
								echo json_encode($arr);
							}
						} else {
							$houseHand = drawHouse($houseHand, $deck);
							if(checkPoints($houseHand) > 21) {
								$win = 1;
								
								$query = $db->prepare('UPDATE blackjack SET win = ? WHERE ID = ?');
								$query->bind_param('ii', $win, $_GET['game']);
					
								$query->execute();
									
								$query = $db->prepare('SELECT * FROM users WHERE username = ?');
								$query->bind_param('s', $_COOKIE['username']);
								
								$query->execute();
									
								$result = $query->get_result();
								while ($row = $result->fetch_assoc()) { 
									$balance = $row['balance'];
									$losted = $row['losted'];
									$won = $row['won'];
								}
									
								$balance += ($bet + $bet);
								$won += $bet;
								$losted -= $bet;
													
								$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
								$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
								
								$query->execute();
								
								$query = $db->prepare('UPDATE history SET win = ?, reward = ? WHERE transType = ? AND gameid = ?');
								$query->bind_param('idii', $win, $bet, $trans, $_GET['game']);
									
								$query->execute();
									
								$arr = array('status' => 'success', 'secret' => $secret, 'message' => 'You have won.', 'housePoints' => checkPoints($houseHand), 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $houseHand);
								echo json_encode($arr);
							} else {
								if(checkPoints($houseHand) > checkPoints($playerHand)) {
									$win = 2;
									
									$query = $db->prepare('UPDATE blackjack SET win = ? WHERE ID = ?');
									$query->bind_param('ii', $win, $_GET['game']);
					
									$query->execute();
									
									$arr = array('status' => 'success', 'secret' => $secret, 'message' => 'House had more points than you.', 'housePoints' => checkPoints($houseHand), 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $houseHand);
									echo json_encode($arr);
								} else if(checkPoints($houseHand) == checkPoints($playerHand)) {
									$win = 3;
									
									$query = $db->prepare('UPDATE blackjack SET win = ? WHERE ID = ?');
									$query->bind_param('ii', $win, $_GET['game']);
						
									$query->execute();
										
									$query = $db->prepare('SELECT * FROM users WHERE username = ?');
									$query->bind_param('s', $_COOKIE['username']);
									
									$query->execute();
										
									$result = $query->get_result();
									while ($row = $result->fetch_assoc()) { 
										$balance = $row['balance'];
										$losted = $row['losted'];
										$won = $row['won'];
									}
										
									$balance += $bet;
									$losted -= $bet;
														
									$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
									$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
									
									$query->execute();
										
									$query = $db->prepare('UPDATE history SET win = ?, reward = ? WHERE transType = ? AND gameid = ?');
									$query->bind_param('idii', $win, $bet, $trans, $_GET['game']);
										
									$query->execute();
										
									$arr = array('status' => 'success', 'secret' => $secret, 'message' => 'Draw', 'points' => checkPoints($playerHand), 'housePoints' => checkPoints($houseHand), 'win' => $win, 'house' => $houseHand);
									echo json_encode($arr);
								} else {
									$win = 1;
									
									$query = $db->prepare('UPDATE blackjack SET win = ? WHERE ID = ?');
									$query->bind_param('ii', $win, $_GET['game']);
						
									$query->execute();
										
									$query = $db->prepare('SELECT * FROM users WHERE username = ?');
									$query->bind_param('s', $_COOKIE['username']);
									
									$query->execute();
										
									$result = $query->get_result();
									while ($row = $result->fetch_assoc()) { 
										$balance = $row['balance'];
										$losted = $row['losted'];
										$won = $row['won'];
									}
										
									$balance += ($bet + $bet);
									$won += $bet;
									$losted -= $bet;
														
									$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
									$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
									
									$query->execute();
									
									$query = $db->prepare('UPDATE history SET win = ?, reward = ? WHERE transType = ? AND gameid = ?');
									$query->bind_param('idii', $win, $bet, $trans, $_GET['game']);
										
									$query->execute();
										
									$arr = array('status' => 'success', 'secret' => $secret, 'message' => 'You have won.', 'housePoints' => checkPoints($houseHand), 'points' => checkPoints($playerHand), 'win' => $win, 'house' => $houseHand);
									echo json_encode($arr);
								}
							}
						}
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
	} else if($_GET['action'] == "split") { 
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
				$insurance = $row['insurance'];
				$player = $row['player'];
				$ssecret = $row['secret'];
				
				$deck = json_decode($deckString);
				$playerHand = json_decode($playerHandString);
				$houseHand = json_decode($houseHandString);
				
				}
				
				//We're now checking if the game did not ended, if the player is the same and if no hit happend.
				if($win == 0) {
					if($player == $_COOKIE['username']) {
						if($state == 0) {
							$query = $db->prepare('SELECT * FROM users WHERE username = ?');
							$query->bind_param('s', $_COOKIE['username']);
								
							$query->execute();
									
							$result = $query->get_result();
							while ($row = $result->fetch_assoc()) { 
								$balance = $row['balance'];
								$losted = $row['losted'];
								$won = $row['won'];
							}
							
							//We're now checking if the player has enough balance.
							if($balance >= $bet) {
								//We're checking if the cards are the same.
								if($playerHand[0][0] == $playerHand[1][0]) {
									$balance -= $bet;
									$losted += $bet;
									
									//Now we are saving the second card in other variable and removing it from the hand and drawing a new card.
									$splitCard = $playerHand[1][0];
									$newDeck = array_pop($playerHand);
									$newDeck = drawCards($deck, 1, $newDeck);
									$deck = removeCards($deck);
									
									//Now we are checking to see if the new first hand is a blackjack and the house is not. If it is we reward the player and go to the second hand.
									if(checkPoints($newDeck) == 21 && checkPoints($houseHand) != 21) {
										//We're drawing the second card for the second deck and checking if it's a blackjack too!
										$secondNewDeck = drawCards($deck, 1, $splitCard);
										$deck = removeCards($deck);
										if(checkPoints($secondNewDeck) == 21) {
											$balance += ($bet * 4);
										}
									}
								} else {
									$arr = array('status' => 'error', 'message' => 'You can\'t split two different cards.');
									echo json_encode($arr);
								}
							} else {
								$arr = array('status' => 'error', 'message' => 'You dont have enough SBD.');
								echo json_encode($arr);
							}
						} else {
						$arr = array('status' => 'error', 'error' => 981, 'message' => 'You can\'t split now.');
						echo json_encode($arr);
						}
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
	} else if($_GET['action'] == "doubled") { 
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
				$insurance = $row['insurance'];
				$player = $row['player'];
				$ssecret = $row['secret'];
				
				$deck = json_decode($deckString);
				$playerHand = json_decode($playerHandString);
				$houseHand = json_decode($houseHandString);
				
				}
				
				//We're now checking if the game did not ended, if the player is the same and if no hit happend.
				if($win == 0) {
					if($player == $_COOKIE['username']) {
						if($state == 0) {
							$query = $db->prepare('SELECT * FROM users WHERE username = ?');
							$query->bind_param('s', $_COOKIE['username']);
								
							$query->execute();
									
							$result = $query->get_result();
							while ($row = $result->fetch_assoc()) { 
								$balance = $row['balance'];
								$losted = $row['losted'];
								$won = $row['won'];
							}
							
							//We're now checking if the player has enough balance.
							if($balance >= $bet) {
								$balance -= $bet;
								$losted += $bet;
								
								//Now we are giving one card to the player.
								$playerHand = drawCards($deck, 1, $playerHand);
								
								$deck = removeCards($deck);

								$playerHandString = json_encode($playerHand);
								
								$house = $houseHand;
								//Now we are checking every possible situation.
								//If the player has over 21.
								if(checkPoints($playerHand) > 21) {
									$win = 2;
									$status = 1;
								}//If the player has 21
								else if(checkPoints($playerHand) == 21) {
									//And house has blackjack, he loses.
									if(checkPoints($houseHand) == 21) {
										$win = 2;
										$status = 2;
										//If he had insurance, he'll get 1x bet.
										if($insurance) {
											$balance += $bet;
											$losted -= $bet;
										}

									}
									//Else, if the house has under 21 but over 16, player wins
									else if(checkPoints($houseHand) > 16) {
										$win = 1;
										$status = 3;
										$balance += ($bet*2*2);
										$losted -= ($bet*2);
										$won += ($bet*2);
									} else { //Else if the house has under 17, it calculates how much to draw. (Hits until points bigger than 17)
										$houseHand = drawHouse($houseHand, $deck);
										$houseHandString = json_encode($houseHand);
										
										//If the house draws 21, then it's a draw
										if(checkPoints($houseHand) == 21) {
											$win = 3;
											$status = 4;
											$balance += ($bet*2);
											$losted -= ($bet*2);
										//If the house draws over 21, then player wins
										} else if(checkPoints($houseHand) > 21) {
											$win = 1;
											$status = 5;
											$balance += ($bet*2*2);
											$losted -= ($bet*2);
											$won += ($bet*2);
										} else { //If the house draws under 21, then player wins
											$win = 1;
											$status = 6;
											$balance += ($bet*2*2);
											$losted -= ($bet*2);
											$won += ($bet*2);
										}
									}
								} else { //If the player has under 21
									//And the house over 16
									if(checkPoints($houseHand) > 16) {
										//If the house has more points than the player, the player loses.
										if(checkPoints($houseHand) > checkPoints($playerHand)) {
											$win = 2;
											$status = 7;
										} //Else if the player has more points, he wins
										else if(checkPoints($houseHand) < checkPoints($playerHand)) {
											$win = 1;
											$status = 6;
											$balance += ($bet*2*2);
											$losted -= ($bet*2);
											$won += ($bet*2);
										} //Else the house and the player have the same points, so it's a draw
										else {
											$win = 3;
											$status = 4;
											$balance += ($bet*2);
											$losted -= ($bet*2);
										}
									} //Else the house has under 17 and will draw until gets at least 17
									else {
										$houseHand = drawHouse($houseHand, $deck);
										$houseHandString = json_encode($houseHand);
										
										//If the house has over 21, then the player wins.
										if(checkPoints($houseHand) > 21) {
											$win = 1;
											$status = 5;
											$balance += ($bet*2*2);
											$losted -= ($bet*2);
											$won += ($bet*2);
										} //Else if the player has more points than the house, the player wins.
										else if(checkPoints($houseHand) < checkPoints($playerHand)) {
											$win = 1;
											$status = 6;
											$balance += ($bet*2*2);
											$losted -= ($bet*2);
											$won += ($bet*2);
										} //Else if the house has more points, the house wins.
										else if(checkPoints($houseHand) > checkPoints($playerHand)) {
											$win = 2;
											$status = 7;
										} //Else it's a draw.
										else {
											$win = 3;
											$status = 4;
											$balance += ($bet*2);
											$losted -= ($bet*2);
										}
									}
								}
									//We're updating the player balance.
								$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
								$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
											
								$query->execute();
								
									//And the blackjack game win status.
								$query = $db->prepare('UPDATE blackjack SET win = ? WHERE ID = ?');
								$query->bind_param('ii', $win, $_GET['game']);
											
								$query->execute();
									
									//And sending the new info to the player.
								$arr = array('status' => 'success', 'message' => 'Double down successfully.', 'balance' => $balance, 'win' => $win, 'statuss' => $status, 'player' => $playerHand, 'house' => $houseHand, 'housePoints' => checkPoints($houseHand), 'secret' => $ssecret, 'points' => checkPoints($playerHand));
								echo json_encode($arr);
							} else {
								$arr = array('status' => 'error', 'message' => 'You dont have enough SBD.');
								echo json_encode($arr);
							}
						} else {
							$arr = array('status' => 'error', 'error' => 981, 'message' => 'You can\'t double down now.');
							echo json_encode($arr);
						}
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
	} else if($_GET['action'] == "surrender") { 
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
						if($state == 0) {
							$query = $db->prepare('SELECT * FROM users WHERE username = ?');
							$query->bind_param('s', $_COOKIE['username']);
								
							$query->execute();
									
							$result = $query->get_result();
							while ($row = $result->fetch_assoc()) { 
								$balance = $row['balance'];
								$losted = $row['losted'];
								$won = $row['won'];
							}
							
							$balance += ($bet/2);
							$losted -= ($bet/2);
							
							$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
							$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
									
							$query->execute();
							
							$win = 4;
							
							$query = $db->prepare('UPDATE blackjack SET win = ? WHERE ID = ?');
							$query->bind_param('ii', $win, $_GET['game']);
						
							$query->execute();
							
							$arr = array('status' => 'success', 'message' => 'Game has been successfully forfeit.', 'balance' => $balance);
							echo json_encode($arr);
						} else {
							$arr = array('status' => 'error', 'error' => 981, 'message' => 'You can\'t surrender now.');
							echo json_encode($arr);
						}
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
	} else if($_GET['action'] == "insurance") { 
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
				$insurance = $row['insurance'];
				$player = $row['player'];
				
				$deck = json_decode($deckString);
				$playerHand = json_decode($playerHandString);
				$houseHand = json_decode($houseHandString);
				
				}
				
				//We're now checking if the game did not ended, if the player is the same and if there is no insurance already and if the player hit.
				if($win == 0) {
					if($player == $_COOKIE['username']) {
						if($state == 0 && $insurance == 0) {
							$query = $db->prepare('SELECT * FROM users WHERE username = ?');
							$query->bind_param('s', $_COOKIE['username']);
								
							$query->execute();
									
							$result = $query->get_result();
							while ($row = $result->fetch_assoc()) { 
								$balance = $row['balance'];
								$losted = $row['losted'];
								$won = $row['won'];
							}
							
							//We're now checking if the player has enough balance and if first card is a 10, ace, jack, queen or king.
							if($balance >= ($bet/2)) {
								if($houseHand[0][0] == 11) {
									
									$balance -= ($bet/2);
									$losted += ($bet/2);
									
									$query = $db->prepare('UPDATE users SET balance = ?, losted = ?, won = ? WHERE username = ?');
									$query->bind_param('ddds', $balance, $losted, $won, $_COOKIE['username']);
											
									$query->execute();
									
									$insurance = 1;
									
									$query = $db->prepare('UPDATE blackjack SET insurance = ? WHERE ID = ?');
									$query->bind_param('ii', $insurance, $_GET['game']);
								
									$query->execute();
									
									$arr = array('status' => 'success', 'message' => 'Insurance put successfully.', 'balance' => $balance);
									echo json_encode($arr);
								} else {
									$arr = array('status' => 'error', 'message' => 'The first card of the dealer is not an ace.');
									echo json_encode($arr);
								}
							} else {
								$arr = array('status' => 'error', 'message' => 'You dont have enough SBD.');
								echo json_encode($arr);
							}
						} else {
							$arr = array('status' => 'error', 'error' => 981, 'message' => 'You can\'t use insurance now.');
							echo json_encode($arr);
						}
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
	} else {
		$arr = array('status' => 'error', 'error' => 802, 'message' => 'Action is invalid.');
		echo json_encode($arr);
	}
} else {
	$arr = array('status' => 'error', 'error' => 802, 'message' => 'Action is invalid.');
	echo json_encode($arr);
}
?>