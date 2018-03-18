<?php
include_once('src/config.php');
if(isset($_GET['balanceTop']))
	if($_GET['balanceTop'] != 0)
	{
		if($_GET['action'] == "newgame")
			$url = "https://steemconnect.com/sign/transfer?from=".$_COOKIE['username']."&to=".$botaccount."&amount=".$_GET['balanceTop']."%20SBD&memo=play%20coinflip&redirect_uri=".$websiteadress."/added.php";
		else
			$url = "https://steemconnect.com/sign/transfer?from=".$_COOKIE['username']."&to=".$botaccount."&amount=".$_GET['balanceTop']."%20SBD&memo=coinflip%20".$_GET['game']."&redirect_uri=".$websiteadress."/added.php";
		header("Location: ".$url);
		die();
	}
?>
<html>
	<head><title>SteemCasino </title></head>
	<body>
		How much would you like to bet?
		<form>
			<input type="number" step=".001" min="0.001" pattern="\d+(\.\d{2})?" class="form-control" id="balanceTop" name="balanceTop">
			<input type="hidden" name="action" value="<?php echo $_GET['action']; ?>">
			<?php 
				if(isset($_GET['game']))
					echo '<input type="hidden" name="game" value="'.$_GET['game'].'">';
			?>
			<input type="submit" value="Submit">
		</form>
	</body>
</html>