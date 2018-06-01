<?php
include_once('src/db.php');
include_once('src/gamesutils.php');

	if(!empty($_GET["username"]) && !empty($_GET["access_token"]) && !empty($_GET["expires_in"]))
	{
		$expiresIn = $_GET["expires_in"];
		setcookie("username", $_GET["username"], time()+$expiresIn, "/");
		setcookie("access_token", $_GET["access_token"], time()+$expiresIn, "/");
		setcookie("expires_in", time()+$expiresIn, time()+$expiresIn, "/");
	}
	
	$query = $db->prepare('SELECT * FROM users WHERE username = ?');
	$query->bind_param('s', $_GET['username']);
	
	$query->execute();
	
	$result = $query->get_result();
	if(!$result->num_rows) {
		$balance = 0;
		$dices = generateSecret()."-".generateSecret();
		$slots = generateSecret(98)."-".generateSecret();
		$query = $db->prepare('INSERT INTO users (`username`, `balance`, `dicesecret`, `slotsecret`) VALUES (?, ?, ?, ?)');
		$query->bind_param('siss', $_GET['username'], $balance, $dices, $slots);
	
		$query->execute();
	} else {
		while ($row = $result->fetch_assoc()) { 
			setcookie("privacy", $row['privacy'], time()+$expiresIn, "/");
		}
	}
?>
<html lang="en">
	<head>
		<?php include_once("src/head.php"); ?>
	</head>
	<body>
		<?php include ("navbar.php"); ?>
		<center><p id="redirect">
			You will be redirected automatically in 3 seconds.<br/>
			If not click<a href="index.php" id="clickhere"> here</a>.
		</p></center>

		<script>
			setTimeout(function(){$(location).attr('href', 'index.php');}, 3000);
		</script>
		<?php include('src/footer.php'); ?>
	</body>
</html>
