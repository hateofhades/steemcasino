<?php
include_once('src/config.php');

include_once('src/db.php');

include_once('src/coinfliputils.php');

if(isset($_GET['game'])) {
	if(!$_GET['game'] == NULL) {
		if($_GET['game'] == 0)
			die("Invalid gameID.");
			$query = $db->prepare('SELECT * FROM users WHERE username = ?');
			$query->bind_param('s', $_COOKIE['username']);
	
			$query->execute();
			
			$result = $query->get_result();
			if($result->num_rows) {
				$token = $_COOKIE['access_token'];
				while ($row = $result->fetch_assoc()) { 
					$balanced = $row['balance'];
					$hash = $row['token'];
				}
				if(password_verify($token, $hash)) {
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
						$secret = $row['secret'];
					}
					if($balanced < $bet)
						die("You don't have enough money!");
					if($player2 == "") {
						$playered = 2;
						$otherplayer = $player1;
					}
					else {
						$playered = 1;
						$otherplayer = $player2;
					}
					
					if($secret[0] == "A")
						$win = 1;
					else
						$win = 2;
					
					$query = $db->prepare('UPDATE coinflip SET player'.$playered.' = ?, win = ? WHERE ID = ?');
					$query->bind_param('sii', $_COOKIE['username'], $win, $_GET['game']);
					
					$query->execute();
					if($playered = $win)
					{
						$newbalance = $balanced + $bet;
						$won = 4;
					} else {
						$newbalance = $balance - $bet;
						
						$query = $db->prepare('SELECT * FROM users WHERE username = ?');
						$query->bind_param('s', $otherplayer);
						
						$query->execute();
						$result = $query->get_result();
						while ($row = $result->fetch_assoc()) { 
							$otherbalance = $row['balance'];
						}
						
						$otherbalance = $otherbalance + $bet;
						
						$query = $db->prepare('UPDATE users SET balance = ? WHERE username = ?');
						$query->bind_param('ds', $otherbalance, $otherplayer);
						
						$query->execute();	
					}
					
					$query = $db->prepare('UPDATE users SET balance = ? WHERE username = ?');
					$query->bind_param('ds', $newbalance, $_COOKIE['username']);
					
					$query->execute();
					echo "<script>
							window.onunload = refreshParent;
							function refreshParent() {
								window.opener.location.reload();
							}
							window.close();
						</script>";					
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