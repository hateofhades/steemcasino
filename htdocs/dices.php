<html>
	<head>
		<?php include('src/head.php'); ?>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div id="dicesBody">
			<center><h1 style="margin-bot:0">Dices</h1></center>
			<div id="dicesGame">
				<b style="float:left;margin-left:10px;margin-top:10px;">Bet Amount</b>
				<b style="float:right;margin-right:10px;margin-top:10px;">Profit On Win</b><br><br>
				<input id="dicesInput" style="margin-left:10px;" type="number" step=".001" min="0.001" value="1" pattern="\d+(\.\d{2})?" name="bet">
				<button class="dicesButton" id="dicesx2" onClick="doubleDices();">x2</button>
				<button class="dicesButton" id="dices12" onClick="halfDices();">1/2</button>
				<b style="float:right;margin-right:10px;font-size:40px;color:white" id="profit">2</b><br><br>
				<b style="margin-left:10px;">Roll </b><b style="color:#00c74d">under</b>
				<b style="margin-left:14%">Multiplier</b>
				<b style="margin-left:14%">Win Chance</b><br><br>
				<input style="margin-left:10px;" type="number" class="dicesChances" id="rollUnder" min="0">
				<input style="margin-left:10px;" type="number" class="dicesChances" id="multiplier" min="0">
				<input style="margin-left:10px;" type="number" class="dicesChances" id="winChance" min="0"><br><br>
				<button class="rollButton" id="rollButton" onclick="rollDices();">ROLL!</button>
			</div>
		</div>
		<?php include('src/footer.php'); ?>
	</body>
</html>