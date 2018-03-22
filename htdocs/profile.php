<?php
include_once('src/db.php');

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
?>
<html>
	<head>
		<?php include('src/head.php'); ?>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<center>
			<h1 id="accountName">Loading...<script>
			sc2.me(function (err, result)
			{
				if (!err)
				{
					console.log(result);
					$("#accountName").text(Cookies.get("username") + " (");
					var reputation = result.account.reputation;
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
			<a href="#" id="topup" style="display:inline;text-decoration:none;color:black;"  onClick="MyWindow=window.open('balance.php?action=deposit','MyWindow',width=600,height=300); return false;"></a> 
			<p id="lll" style="display:inline;text-decoration:none;color:black;"></p> 
			<a href="#" id="withdraw" style="display:inline;text-decoration:none;color:black;"  onClick="MyWindow=window.open('balance.php?action=withdrawal','MyWindow',width=600,height=300); return false;"></a>
			<h3 id="profit" style="margin-bottom:0"></h3>
			<h5 id="totals" style="margin-top:0"></h4>
		</center>
		<?php include('src/footer.php'); ?>
	</body>
</html>