<html>
	<head>
		<?php include('src/head.php'); ?>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div>
			<div id="messages-box">
				<p id="messages" style="display:inline"></p>
				<a href="#" id="closeMessage" onclick="closeMessage()"></a>
			</div>
			<div class="blackjack-body">
				<center><h1 style="display:inline">Blackjack </h1><b><a href="games.php" style="display:inline;text-decoration:none;color:black;">(Go back) </a></b></center>
				<div id="blackjack-table">
					<center><p style="margin:0" id="gameStatus">Let's play!</p></center>

					<p style="margin-left:25px" id="dealerHandString">Dealer:</p>
					<div id="dealerHand" id="houseHandString">
						
					</div>
					<p style="margin-left:25px" id="playerHandString">Player:</p>
					<div id="playerHand">
					
					</div><br>
					<div class="bjbuttons" onClick="deal()" id="deal">Deal</div>
					<div class="bjbuttons" onClick="hit()" id="hit">Hit</div>
					<div class="bjbuttons" onClick="stand()" id="stand">Stand</div>
					<div class="bjbuttons" onClick="insurance()" id="insurance">Insurance</div>
					<div class="bjbuttons" onClick="doubled()" id="doubled">Double Down</div>
					<div class="bjbuttons" onClick="split()" id="split">Split</div>
					<div class="bjbuttons" onClick="surrender()" id="surrender">Surrender</div>
					<div style="width:100%;height:1.5%"></div>
					<center><p style="margin:0">Bet</p><input type="number" step=".001" min="0.001" value="1" pattern="\d+(\.\d{2})?" id="bet" name="bet"></center>
				</div>
			</div>
			<center>
				<p id="hash"></p>
				<p id="secret"></p>
			</center>
		</div>
		<script src="js/blackjack.js"></script>
		<?php include('src/footer.php'); ?>
	</body>
</html>