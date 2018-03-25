<?php
include_once('src/config.php');

include_once('src/db.php');

include_once('src/head.php');

include_once('src/coinfliputils.php');

include_once('src/utils.php');

if(isset($_GET['player'])) {
	if(!$_GET['game'] == NULL) {
		if($_GET['game'] == 0)
			die("Invalid gameID.");
		if($_GET['player'] == 1 || $_GET['player'] == 2 || $_GET['player'] == 3) {
			$player2pick = $_GET['player'];
			$query = $db->prepare('SELECT * FROM users WHERE username = ?');
			$query->bind_param('s', $_COOKIE['username']);
		
			$query->execute();
				
			$result = $query->get_result();
			if($result->num_rows) {
				while ($row = $result->fetch_assoc()) { 
					$balanced = $row['balance'];
					$thiswon = $row['won'];
					$thislost = $row['losted'];
				}
					
				if(IsLoggedOnUser()) {
					$query = $db->prepare('SELECT * FROM rps WHERE ID = ?');
					$query->bind_param('i', $_GET['game']);
							
					$query->execute();
					$result = $query->get_result();
					
					if(!$result->num_rows)
						die("Invalid gameID.");
					
					while ($row = $result->fetch_assoc()) { 
						$bet = $row['bet'];
						$player1 = $row['player1'];
						$player2 = $row['player2'];
						$reward = $row['reward'];
						$player1pick = $row['player1pick'];
					}
					
					if($balanced < $bet)
						die("You don't have enough money!");
					
					if($player2 != "" && $player1 != "")
						die("Game has already ended.");
					
					if($player1 == $_COOKIE['username'])
						die("You can't play in your own games!");
					
					if($player1pick == $player2pick)
						$winning = 3;
					else if(($player1pick == 1 && $player2pick == 3) || ($player1pick == 2 && $player2pick == 1) || ($player1pick == 3 && $player2pick == 2))
						$winning = 1;
					else
						$winning = 2;
					
					$timestamp = time();
					
					$query = $db->prepare('UPDATE rps SET player2 = ?, player2pick = ?, win = ?, timestamp = ? WHERE ID = ?');
					$query->bind_param('siiii', $_COOKIE['username'], $player2pick, $winning, $timestamp, $_GET['game']);
					
					$query->execute();
					
					$query = $db->prepare('SELECT * FROM users WHERE username = ?');
					$query->bind_param('s', $player1);
						
					$query->execute();
					$result = $query->get_result();
					while ($row = $result->fetch_assoc()) { 
						$otherbalance = $row['balance'];
						$otherwon = $row['won'];
						$otherlost = $row['losted'];
					}
					
					if($winning == 2)
					{
						$newbalance = $balanced + $bet;
						$thiswon = $thiswon + $bet;
						$otherlost = $otherlost + $bet;
						
					} else if($winning == 1){
						$newbalance = $balanced - $bet;
						$thislost = $thislost + $bet;
						$otherwon = $otherwon + $bet;
						$otherbalance = $otherbalance + $reward;
					} else {
						$newbalance = $balanced;
						$otherbalance = $otherbalance + $bet;
					}
					
					$query = $db->prepare('UPDATE users SET balance = ?, won = ?, losted = ? WHERE username = ?');
					$query->bind_param('ddds', $otherbalance, $otherwon, $otherlost, $player1);
						
					$query->execute();	
					
					$query = $db->prepare('UPDATE users SET balance = ?, won = ?, losted = ? WHERE username = ?');
					$query->bind_param('ddds', $newbalance, $thiswon, $thislost, $_COOKIE['username']);
					
					$query->execute();
					
					echo "<script>
						window.onunload = refreshParent;
						function refreshParent() {
							window.opener.location.reload();
						}
						window.close();
					</script>";	
				} else {
					echo '<p style="color:red">You don\'t have enough balance. Balance: '.$balanced.' SBD</p>';
				}
			} else {
				echo '<p style="color:red">Error 1: Your session is invalid! Please relog.</p>';
			}
		} else {
			echo '<p style="color:red">Error 2: Your session is invalid! Please relog.</p>';
		}
	}
}
?>
<html style="font-family: Arial;">
	<head>
		<title>SteemCasino </title>
		<?php include_once('src/head.php'); ?>
	</head>
	<body>
		<center>Rock, paper, scissors?
		<form>
			<?php 
				if(isset($_GET['game']))
					echo '<input type="hidden" name="game" value="'.$_GET['game'].'">';
			?>
			Choose your side!
			<br><br>
			<input type="radio" name="player" value="1" checked="checked">Rock
			<input id="bitcoin" type="radio" name="player" value="2">Paper
			<input id="bitcoin" type="radio" name="player" value="2">Scissors
			<br><br>
			<input type="submit" value="Submit">
		</form></center>
	</body>
</html>