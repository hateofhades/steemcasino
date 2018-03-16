<?php
include_once('src/config.php');
if(isset($_GET['balanceTop']))
	if($_GET['balanceTop'] != 0)
	{
		$url = "https://steemconnect.com/sign/transfer?from=".$_COOKIE['username']."&to=".$botaccount."&amount=".$_GET['balanceTop']."%20SBD&memo=deposit&redirect_uri=".$websiteadress."/added.php";
		header("Location: ".$url);
		die();
	}
?>
<html>
	<head><title>SteemCasino - Top up balance</title></head>
	<body>
		How much would you like to add?
		<form>
			<input type="number" step=".001" min="0.001" pattern="\d+(\.\d{2})?" class="form-control" id="balanceTop" name="balanceTop">
			<input type="submit" value="Submit">
		</form>
	</body>
</html>