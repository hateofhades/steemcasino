<?php
include_once('src/config.php');
if(isset($_GET['balanceTop']))
	if($_GET['balanceTop'] != 0)
	{
		$urldeposit = "https://steemconnect.com/sign/transfer?from=".$_COOKIE['username']."&to=".$botaccount."&amount=".$_GET['balanceTop']."%20SBD&memo=deposit&redirect_uri=".$websiteadress."/added.php";
		$urlwithdraw = "https://steemconnect.com/sign/transfer?from=".$_COOKIE['username']."&to=".$botaccount."&amount=0.001%20SBD&memo=withdraw%20".$_GET['balanceTop']."&redirect_uri=".$websiteadress."/added.php";
		if($_GET['action'] == "deposit")
			header("Location: ".$urldeposit);
		else 
			header("Location: ".$urlwithdraw);
		die();
	}
?>
<html>
	<head><title>SteemCasino - Top up balance</title></head>
	<body>
		How much would you like to <?php if($_GET['action'] == "deposit") echo "deposit"; else echo "withdraw"; ?>?
		<form>
			<input type="number" step=".001" min="0.001" pattern="\d+(\.\d{2})?" class="form-control" id="balanceTop" name="balanceTop">
			<input type="hidden" name="action" value="<?php if($_GET['action'] == "deposit") echo "deposit"; else echo "withdraw"; ?>">
			<input type="submit" value="Submit">
		</form>
	</body>
</html>