<?php
include_once('src/db.php');
include_once('src/utils.php');

if(IsLoggedOnUser()) {
	$balance = 0;
	$query = $db->prepare('SELECT * FROM users WHERE username = ?');
	$query->bind_param('s', $_COOKIE['username']);
		
	$query->execute();
		
	$result = $query->get_result();
	while ($row = $result->fetch_assoc()) {
		$balance = $row['balance'];
		$won = $row['won'];
		$lost = $row['losted'];

		$profit = $won - $lost;
	}

	$query = $db->prepare('SELECT * FROM history WHERE user1 = ? OR user2 = ?');
	$query->bind_param('ss', $_COOKIE['username'], $_COOKIE['username']);

	$query->execute();
	
	$result = $query->get_result();
	while ($row = $result->fetch_assoc()) {
		if($row['transType'] == 1) {
			
		} else if($row['transType'] == 2) {
			
		} else if($row['transType'] == 3) {
			
		} else if($row['transType'] == 4) {
			
		} else if($row['transType'] == 5) {
			
		} else if($row['transType'] == 6) {
			
		} else if($row['transType'] == 7) {
			
		}
	}
}
?>
<html>
	<head>
		<?php include('src/head.php'); ?>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<center>
			<div class="default-body">
				<div style="margin-top:2%;vertical-align:center">
					<img id="accountPicture" style="display:none;margin-right:2%;margin-top:5px" width="60px" height="60px"><h1 id="accountName" style="display:inline;margin-top:0;">Loading...</h1>
				</div>
				<br>
				<script>
				sc2.me(function (err, result)
				{
					if (!err)
					{
						console.log(result);
						$("#accountName").text(Cookies.get("username") + " (");
						var reputation = result.account.reputation;
						var profileImage = JSON.parse(result.account.json_metadata)['profile']['profile_image'];
						$("#accountPicture").attr("src",profileImage);
						$("#accountPicture").css("border-radius", "60px");
						$("#accountPicture").css("display", "inline");
						console.log(profileImage);
						reputation = log10(reputation);
						reputation = reputation - 9;
						reputation = reputation * 9;
						reputation = reputation + 25;
						reputation = Math.floor(reputation);
						$("#accountName").append(reputation + ")");
						$("#balance").text("Balance: <?php echo $balance;?> SBD ");
						$("#topup").text(" Deposit");
						$("#withdraw").text(" Withdraw");
						$("#lll").text(" / ");
						$("#profit").text("Profit: <?php echo $profit." SBD";?>");
						$("#totals").text("<?php echo "(Wins: ".$won." SBD | Loses: ".$lost." SBD )";?>");
					}
				});
				</script></h1>
				<h3 id="balance" style="display:inline"></h3>
				<a href="#" id="topup"  onClick="MyWindow=window.open('balance.php?action=deposit','MyWindow',width=600,height=300); return false;"></a> 
				<p id="lll" style="display:inline"></p> 
				<a href="#" id="withdraw" onClick="MyWindow=window.open('balance.php?action=withdrawal','MyWindow',width=600,height=300); return false;"></a>
				<h3 id="profit" style="margin-bottom:0"></h3>
				<h5 id="totals" style="margin-top:0"></h4>
			</div>
		</center>
		<?php include('src/footer.php'); ?>
	</body>
</html>