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
					$("#accountName").text(Cookies.get("username") + " (");
					var reputation = result.account.reputation;
					reputation = log10(reputation);
					reputation = reputation - 9;
					reputation = reputation * 9;
					reputation = reputation + 25;
					reputation = Math.floor(reputation);
					$("#accountName").append(reputation + ")");
				}
			});
			</script></h1>
		</center>
	</body>
</html>