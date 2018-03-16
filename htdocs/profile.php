<?php
include_once('src/db.php');

$balance = 0;
$query = $db->prepare('SELECT * FROM users WHERE username = ?');
$query->bind_param('s', $_COOKIE['username']);
	
$query->execute();
	
$result = $query->get_result();
while ($row = $result->fetch_assoc()) {
$balance = $row['balance'];
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
					$("#topup").text(" Add more");
				}
			});
			</script></h1>
			<h3 id="balance" style="display:inline"></h3> 
			<a href="#" id="topup" style="display:inline;text-decoration:none;color:black;"  onClick="MyWindow=window.open('topup.php','MyWindow',width=600,height=300); return false;"></a>
		</center>
	</body>
</html>