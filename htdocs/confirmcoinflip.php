<?php
include_once('src/config.php');

include_once('src/db.php');

include_once('src/head.php');

include_once('src/gamesutils.php');

include_once('src/utils.php');

if(isset($_GET['game'])) {
	if(!$_GET['game'] == NULL) {
		if($_GET['game'] == 0)
			die("Invalid gameID.");
			$query = $db->prepare('SELECT * FROM users WHERE username = ?');
			$query->bind_param('s', $_COOKIE['username']);
	
			$query->execute();
			
			$result = $query->get_result();
			if($result->num_rows) {
				while ($row = $result->fetch_assoc()) { 
					$balanced = $row['balance'];
					$thiswon = $row['won'];
					$thislost = $row['losted'];
					$promobal = $row['promob'];
				}
				if(IsLoggedOnUser()) {
					$query = $db->prepare('SELECT * FROM coinflip WHERE ID = ?');
					$query->bind_param('i', $_GET['game']);
							
					$query->execute();
					$result = $query->get_result();
					
					if(!$result->num_rows)
						die("Invalid gameID.");
					
					while ($row = $result->fetch_assoc()) { 
						$bet = $row['bet'];
						$player1 = $row['player1'];
						$reward = $row['reward'];
						$player2 = $row['player2'];
						$hash = $row['hash'];
						$secret = $row['secret'];
					}
					
					if(($balanced + $promobal) < $bet)
						die("You don't have enough money!");
					
					if($player2 != "" && $player1 != "")
						die("Game has already ended.");
					
					if($player2 == "") {
						$playered = 2;
						if($player1 == $_COOKIE['username'])
							die("You can't play in your own games!");
						$otherplayer = $player1;
						$player2 = $_COOKIE['username'];
					}
					else {
						$playered = 1;
						if($player2 == $_COOKIE['username'])
							die("You can't play in your own games!");
						$otherplayer = $player2;
						$player1 = $_COOKIE['username'];
					}
					
					if($secret[0] == "A")
						$win = 1;
					else if($secret[0] == "B")
						$win = 2;
					
					$timestamp = time();
					
					$query = $db->prepare('UPDATE coinflip SET player'.$playered.' = ?, win = ?, timestamp = ? WHERE ID = ?');
					$query->bind_param('siii', $_COOKIE['username'], $win, $timestamp, $_GET['game']);
					
					$query->execute();
					
					$query = $db->prepare('SELECT * FROM users WHERE username = ?');
					$query->bind_param('s', $otherplayer);
						
					$query->execute();
					$result = $query->get_result();
					while ($row = $result->fetch_assoc()) { 
						$otherbalance = $row['balance'];
						$otherwon = $row['won'];
						$otherlost = $row['losted'];
					}
					
					if($playered == $win)
					{
						$newbalance = $balanced + $bet;
						$thiswon = $thiswon + $bet;
						$otherlost = $otherlost + $bet;
					} else{
						$newbalance = $balanced - $bet;
						$thislost = $thislost + $bet;
						$otherwon = $otherwon + $bet;
						$otherbalance = $otherbalance + $reward;
					}
					
					$query = $db->prepare('UPDATE users SET balance = ?, won = ?, losted = ? WHERE username = ?');
					$query->bind_param('ddds', $otherbalance, $otherwon, $otherlost, $otherplayer);
						
					$query->execute();	
					
					$query = $db->prepare('UPDATE users SET balance = ?, won = ?, losted = ? WHERE username = ?');
					$query->bind_param('ddds', $newbalance, $thiswon, $thislost, $_COOKIE['username']);
					
					$query->execute();
					
					$transType = 3;
					
					$query = $db->prepare('INSERT INTO history (transType, amount, gameid, user1, user2, win, reward, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
					$query->bind_param('idissidi', $transType, $bet, $_GET['game'], $player1, $player2, $win, $reward, $timestamp);
					
					$query->execute();
					
					
					echo '
					<script>
						function refreshParent() {
							parent.$("#coinflip-iframe").attr("src", parent.$("#coinflip-iframe").attr("src"));
							parent.$("#iframe").attr("src", "viewcoinflipgame.php?gameid='.$_GET['game'].'&player1='.$player1.'&player2='.$player2.'&bet='.$bet.'&reward='.$reward.'&hash='.$hash.'&secret='.$secret.'");
						}
						
						setTimeout(function () {refreshParent();}, 1);
					</script>
					';
				} else
					die("Your session is invalid. Please relog.");
			} else 
				die("Your session is invalid. Please relog.");
				
				
	} else {
		die("Invalid gameID.");
	}
} else {
	die("Invalid gameID.");
}
?>