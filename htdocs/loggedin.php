<?php
	if(!empty($_GET["username"]) && !empty($_GET["access_token"]) && !empty($_GET["expires_in"]))
	{
		$expiresIn = $_GET["expires_in"];
		setcookie("username", $_GET["username"], time()+$expiresIn, "/");
		setcookie("access_token", $_GET["access_token"], time()+$expiresIn, "/");
		setcookie("expires_in", $expiresIn, time()+$expiresIn, "/");
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<?php include("src/head.php"); ?>
	</head>
	<body>
		<?php include ("navbar.php"); ?>
		<p>
			You will be redirected automatically.<br/>
			If not <a href="index.php">click here</a>.
		</p>

		<script>
			setTimeout(function(){$(location).attr('href', 'index.php');}, 1000);
		</script>

	</body>
</html>
