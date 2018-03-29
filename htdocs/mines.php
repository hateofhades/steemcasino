<html>
	<head>
		<?php include('src/head.php'); ?>
		<script src="js/mines.js"></script>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div class="mines-body">
			<div id="messages-box">
				<p id="messages" style="display:inline"></p>
				<a href="#" id="closeMessage" onclick="closeMessage()"></a>
			</div>
			<a href="#" id="newgame" onClick="newGame(game, bet);">Start new game</a>
			<a href="#" id="cashout" onClick="cashOut(game);"></a> <br>
			Bet : <input type="number" step=".001" min="0.001" value="0.001" pattern="\d+(\.\d{2})?" id="bet" name="bet">
		</div>
		<?php include('src/footer.php'); ?>
	</body>
</html>