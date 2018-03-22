<?php
include_once('src/db.php');

	if(!empty($_GET["username"]) && !empty($_GET["access_token"]) && !empty($_GET["expires_in"]))
	{
		$expiresIn = $_GET["expires_in"];
		setcookie("username", $_GET["username"], time()+$expiresIn, "/");
		setcookie("access_token", $_GET["access_token"], time()+$expiresIn, "/");
		setcookie("expires_in", $expiresIn, time()+$expiresIn, "/");
	}
	
	$query = $db->prepare('SELECT * FROM users WHERE username = ?');
	$query->bind_param('s', $_GET['username']);
	
	$query->execute();
	
	$result = $query->get_result();
	if(!$result->num_rows) {
		$balance = 0;
		$query = $db->prepare('INSERT INTO users (`username`, `balance`) VALUES (?, ?)');
		$query->bind_param('si', $_GET['username'], $balance);
	
		$query->execute();
	}
?>
<html lang="en">
	<head>
		<?php include_once("src/head.php"); ?>
	</head>
	<body>
		<?php include ("navbar.php"); ?>
		<center><p>
			You will be redirected automatically in 3 seconds.<br/>
			If not <a href="index.php">click here</a>.
		</p></center>

		<script>
			setTimeout(function(){$(location).attr('href', 'index.php');}, 3000);
		</script>
		<?php include('src/footer.php'); ?>
	</body>
</html>
