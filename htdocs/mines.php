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
			<center><h1 style="margin-top:0;margin-bot:0">Mines</h1></center>
			<center><a href="#" id="newgame" onClick="newGame(game, bet);">Start new game</a><br>
			<a href="#" id="cashout" onClick="cashOut(game);"></a> <br>
			<span id="betn">Bet :</span><input type="number" step=".001" min="0.001" value="1" pattern="\d+(\.\d{2})?" id="bet" name="bet"></center><br>
			<div id="game">
				<div id="table">
					<center>
					<div style="height:3%;width:100%"></div>
					<a href="#" id="a1" onclick="hitBlock(game, 1);"><div class="mine-box" id="1"></div></a>
					<a href="#" id="a2" onclick="hitBlock(game, 2);"><div class="mine-box" id="2"></div></a>
					<a href="#" id="a3" onclick="hitBlock(game, 3);"><div class="mine-box" id="3"></div></a>
					<a href="#" id="a4" onclick="hitBlock(game, 4);"><div class="mine-box" id="4"></div></a>
					<a href="#" id="a5" onclick="hitBlock(game, 5);"><div class="mine-box" id="5"></div></a>
					<a href="#" id="a6" onclick="hitBlock(game, 6);"><div class="mine-box" id="6"></div></a>
					<a href="#" id="a7" onclick="hitBlock(game, 7);"><div class="mine-box" id="7"></div></a>
					<a href="#" id="a8" onclick="hitBlock(game, 8);"><div class="mine-box" id="8"></div></a>
					<a href="#" id="a9" onclick="hitBlock(game, 9);"><div class="mine-box" id="9"></div></a>
					<a href="#" id="a10" onclick="hitBlock(game, 10);"><div class="mine-box" id="10"></div></a>
					<a href="#" id="a11" onclick="hitBlock(game, 11);"><div class="mine-box" id="11"></div></a>
					<a href="#" id="a12" onclick="hitBlock(game, 12);"><div class="mine-box" id="12"></div></a>
					<a href="#" id="a13" onclick="hitBlock(game, 13);"><div class="mine-box" id="13"></div></a>
					<a href="#" id="a14" onclick="hitBlock(game, 14);"><div class="mine-box" id="14"></div></a>
					<a href="#" id="a15" onclick="hitBlock(game, 15);"><div class="mine-box" id="15"></div></a>
					<a href="#" id="a16" onclick="hitBlock(game, 16);"><div class="mine-box" id="16"></div></a>
					<a href="#" id="a17" onclick="hitBlock(game, 17);"><div class="mine-box" id="17"></div></a>
					<a href="#" id="a18" onclick="hitBlock(game, 18);"><div class="mine-box" id="18"></div></a>
					<a href="#" id="a19" onclick="hitBlock(game, 19);"><div class="mine-box" id="19"></div></a>
					<a href="#" id="a20" onclick="hitBlock(game, 20);"><div class="mine-box" id="20"></div></a>
					<a href="#" id="a21" onclick="hitBlock(game, 21);"><div class="mine-box" id="21"></div></a>
					<a href="#" id="a22" onclick="hitBlock(game, 22);"><div class="mine-box" id="22"></div></a>
					<a href="#" id="a23" onclick="hitBlock(game, 23);"><div class="mine-box" id="23"></div></a>
					<a href="#" id="a24" onclick="hitBlock(game, 24);"><div class="mine-box" id="24"></div></a>
					<a href="#" id="a25" onclick="hitBlock(game, 25);"><div class="mine-box" id="25"></div></a>
					</center>
				</div>
				<div id="messagess">
					<br><p id="hash" class="p-messagess"></p><br><br>
					<p id="secret" class="p-messagess"></p>
				</div>
			</div>
		</div>
		<?php include('src/footer.php'); ?>
	</body>
</html>