<?php
include_once('src/config.php');

include_once('src/db.php');

include_once('src/gamesutils.php');

include_once('src/utils.php');

if(isset($_GET['balanceTop']))
	if($_GET['balanceTop'] != 0 && ($_GET['player'] == 1 || $_GET['player'] == 2 || $_GET['player'] == 3)) {
		if($_GET['action'] == "newgame") {	
			$query = $db->prepare('SELECT * FROM users WHERE username = ?');
			$query->bind_param('s', $_COOKIE['username']);
	
			$query->execute();
			
			$result = $query->get_result();
			if($result->num_rows) {
				while ($row = $result->fetch_assoc()) { 
					$balanced = $row['balance'];
					$ref = $row['reffered'];
				}
				
				if(IsLoggedOnUser()) {
					if($balanced >= $_GET['balanceTop']) {
						if($_GET['player'] == 1) 
							$playered = 1;
						else if($_GET['player'] == 2)
							$playered = 2;
						else
							$playered = 3;

						$reward = $_GET['balanceTop'] * 2;
						
						$newbalance = $balanced - $_GET['balanceTop'];
						
						$query = $db->prepare('INSERT INTO rps (player1, player1pick, bet, reward) VALUES (?, ?, ?, ?)');
						$query->bind_param('sidd', $_COOKIE['username'], $playered, $_GET['balanceTop'], $reward);
						
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
										
							$refbalance = $refbalance + ($_GET['balanceTop']/1000);
						
							$noyou = $db->prepare('UPDATE users SET balance = ? WHERE username = ?');
							$noyou->bind_param('ds', $refbalance, $ref);
							$noyou->execute();
						
							$transType = 8;
							
							$refrew = $_GET['balanceTop']/1000;
							
							$noyou = $db->prepare('INSERT INTO history (transType, user1, user2, reward, timestamp) VALUES (?, ?, ?, ?, ?)');
							$noyou->bind_param('issdi', $transType, $_COOKIE['username'], $ref, $refrew, $timestampedd);
							
							$noyou->execute();
						}
						
						$query = $db->prepare('UPDATE users SET balance = ? WHERE username = ?');
						$query ->bind_param('ds', $newbalance, $_COOKIE['username']);
						
						$query->execute();	
						echo '
						<script>
							parent.$("#coinflip-iframe").attr("src", "rpsgames.php");
							parent.$("#iframe").attr("src", "");
							parent.$(".coinflip-game").hide();
						</script>';
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
	</head>
	<body>
		<center>How much would you like to bet?
		<form>
			<input type="number" step=".001" min="0.001" pattern="\d+(\.\d{2})?" class="form-control" id="balanceTop" name="balanceTop">
			<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">
			<?php 
				if(isset($_GET['game']))
					echo '<input type="hidden" name="game" value="'.$_GET['game'].'">';
			?>
			<br><br>
			Choose your side!
			<br>
			<input type="radio" name="player" value="1" checked="checked">Rock
			<input id="bitcoin" type="radio" name="player" value="2">Paper
			<input id="bitcoin" type="radio" name="player" value="3">Scissors
			<br><br>
			<input type="submit" value="Submit">
		</form></center>
	</body>
</html>
