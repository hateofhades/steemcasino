<html>
	<head>
		<?php include('src/head.php'); ?>
		<script src="js/mines.js"></script>
		<script>
			var user="<?php echo $_COOKIE['username'];?>";
			var token="<?php echo $_COOKIE['access_token'];?>";
		</script>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div class="mines-body">
			<div id="messages-box">
				<p id="messages" style="display:inline"></p>
				<a href="#" id="closeMessage" onclick="closeMessage()"></a>
			</div>
			<a href="#" onClick="newGame(user, token, game);">Start new game</a>
			<a href="#" onClick="cashOut(user, token, game);">Cash out</a>
		</div>
		<?php include('src/footer.php'); ?>
	</body>
</html>