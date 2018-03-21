<?php
include_once('src/config.php');

include_once('src/db.php');

include_once('src/head.php');

include_once('src/coinfliputils.php');

include_once('src/utils.php');

if(isset($_GET['balanceTop']))
	if($_GET['balanceTop'] != 0 && ($_GET['player'] == 1 || $_GET['player'] == 2)) {
		if($_GET['action'] == "newgame") {	
			$query = $db->prepare('SELECT * FROM users WHERE username = ?');
			$query->bind_param('s', $_COOKIE['username']);
	
			$query->execute();
			
			$result = $query->get_result();
			if($result->num_rows) {
				while ($row = $result->fetch_assoc()) { 
					$balanced = $row['balance'];
				}
				
				if(IsLoggedOnUser()) {
					$secret = generateSecret();
					$hashed = hash("whirlpool", $secret);
					if($balanced >= $_GET['balanceTop']) {
						if($_GET['player'] == 1)
							$playered = 1;
						else
							$playered = 2;
						
						$reward = $_GET['balanceTop'] * 2;
						
						$newbalance = $balanced - $_GET['balanceTop'];
						
						$query = $db->prepare('INSERT INTO coinflip (player'.$playered.', bet, reward, secret, hash) VALUES (?, ?, ?, ?, ?)');
						$query->bind_param('sddss', $_COOKIE['username'], $_GET['balanceTop'], $reward, $secret, $hashed);
						
						$query->execute();
						
						$query = $db->prepare('UPDATE users SET balance = ? WHERE username = ?');
						$query ->bind_param('ds', $newbalance, $_COOKIE['username']);
						
						$query->execute();	
						header("Location:added.php");
						die();
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
<html>
	<head>
		<title>SteemCasino </title>
		<?php include_once('src/head.php'); ?>
	</head>
	<body>
		How much would you like to bet?
		<form>
			<input type="number" step=".001" min="0.001" pattern="\d+(\.\d{2})?" class="form-control" id="balanceTop" name="balanceTop">
			<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">
			<?php 
				if(isset($_GET['game']))
					echo '<input type="hidden" name="game" value="'.$_GET['game'].'">';
			?>
			<br>
			Choose your side!
			<br>
			<input type="radio" name="player" value="1" checked="checked">Steem
			<input id="bitcoin" type="radio" name="player" value="2">Bitcoin
			<br>
			<input type="submit" value="Submit" style="margin-left:40px">
		</form>
	</body>
</html>