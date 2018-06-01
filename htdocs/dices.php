<html>
	<head>
		<?php include('src/head.php'); ?>
		<script>
			var animateVar = 0, setRollBackTimeout, rolling = 0, bal, lastRolls = [], updateLastRollsTimeout;
			
			function doubleDices() {
				var currValue = $("#dicesInput").val();
				currValue = currValue * 2;
				$("#dicesInput").val(currValue);
				
				var value = $("#multiplier").val();
					
				$("#profit").text((currValue * value).toFixed(3));
			}
			
			function rollDices() {
				if(!rolling) {
				var bet = $("#dicesInput").val();
				var under = $("#rollUnder").val();
				var secret = $("#secretInput").val();
				
				console.log(secret);
				
				if(bal)
					$("#balance").text("Balance: " + bal + " SBD");
				
				updateLastRolls();
				clearTimeout(updateLastRollsTimeout);
				
				clearTimeout(setRollBackTimeout);
				
				$("#rollButton").css("color", "white");
				$("#rollButton").text("Working...");
				
					rolling = 1;
					$.getJSON( "../src/dices.php?bet=" + bet + "&under=" + under + "&secret=" + secret, function( data ) {
						console.log(data);
						if(data['status'] == 'success') {
							animateVar = 0;
							bal = data['balance'];
							
							$("#currSecret").text("Last roll secret: " + data['secret']);
							$("#diceshash").text("Current hash: " + data['hash']);
							
							var lastRoll = {
								rollUnder: data['under'],
								multiplier: data['multiplier'],
								pick: data['pick'],
								reward: (data['reward']).toFixed(3),
								win: data['win'],
								bet: data['bet'],
							};
							
							lastRolls = {
								roll1: lastRoll,
								roll2: lastRolls['roll1'],
								roll3: lastRolls['roll2'],
							};

							updateLastRollsTimeout = setTimeout(function() {updateLastRolls()}, 5000);
							
							animate(data['pick'], data['win']);
						} else {
							$("#rollButton").text(data['message']);
							rolling = 0;
						}
					});
				}
			}
			
			function animate(pick, win) {
				animateVar++;
				picker = Math.floor(Math.random() * 10000);
				$("#rollButton").text(picker);
				if(animateVar <= 30)
				setTimeout(function () { animate(pick, win) }, 100);
				else {
					rolling = 0;
					$("#rollButton").text(pick);
					
					if(win) 
						$("#rollButton").css("color", "green");
					else
						$("#rollButton").css("color", "red");
					
					setRollBackTimeout = setTimeout(function () { setRollBack() }, 5000);
				}
			}
			
			function setRollBack () {
				$("#rollButton").text("ROLL!");
				$("#rollButton").css("color", "white");
				$("#balance").text("Balance: " + bal + " SBD");
			}
			
			function halfDices() {
				var currValue = $("#dicesInput").val();
				currValue = currValue / 2;
				
				if(currValue < 0.001)
					currValue = 0.001;
				
				$("#dicesInput").val(currValue);
				
				var value = $("#multiplier").val();
					
				$("#profit").text((currValue * value).toFixed(3));
			}
			
			//If value is under min or over max we change it and if we detect a change in Roll Under or Multiplier (done by player) we are updating the other one to correspond.
			$(document).ready(function() {
				$("#secretInput").val(Math.random().toString(36).replace(/[^a-z123456789]+/g, '').substr(0, 10));
				$('#rollUnder').on('input', function() {
					var value = $("#rollUnder").val();
						//value.toFixed(0);
					
					if(value < 10) {
						$("#rollUnder").val(10);
						value = 10;
					}
					else if(value > 9400) {
						$("#rollUnder").val(9400);
						value = 9400;
					}
					
					$("#multiplier").val((9500/value).toFixed(2));
					
					var bet = $("#dicesInput").val();
					
					$("#profit").text((bet * (9500/value)).toFixed(3));
				});
				
				$('#multiplier').on('input', function() {
					var value = $("#multiplier").val();
						//value.toFixed(2);
					
					if(value < 1.01) {
						$("#multiplier").val(1.01);
						value = 1.01;
					}
					else if(value > 9500) {
						$("#multiplier").val(9500);
						value = 9500;
					}
					
					if(value != 1.01)
						$("#rollUnder").val((9500/value).toFixed(0));
					else
						$("#rollUnder").val(9400);
					
					var bet = $("#dicesInput").val();
					
					$("#profit").text((bet * value).toFixed(3));
				});
				
				$('#dicesInput').on('input', function() {
					var bet = $("#dicesInput").val();
					
					if(bet < 0.001) {
						bet = 0.001;
						$("#dicesInput").val(bet);
					}
					
					var value = $("#multiplier").val();
					
					$("#profit").text((bet * value).toFixed(3));
				});
			});
			
			function updateLastRolls() {
				var lastRollsDiv = "", winz, winz2;
				if(lastRolls['roll3']) {
					if(lastRolls['roll3']['win']) {
						winz = "<h1 id=\"under-win\">";
						winz2 = "+" + lastRolls['roll3']['reward'];
					}
					else {
						winz = "<h1 id=\"under-lose\">";
						winz2 = "-" + lastRolls['roll3']['bet'];
					}
					
					lastRollsDiv = "<div id=\"lastRoll\"><center>" + winz + "<" + lastRolls['roll3']['rollUnder'] + " | x" + lastRolls['roll3']['multiplier'] + " | " + winz2 + " SBD | " + lastRolls['roll3']['pick'] + "</h3></center></div>" + lastRollsDiv;
				}
				
				if(lastRolls['roll2']) {
					if(lastRolls['roll2']['win']) {
						winz = "<h1 id=\"under-win\">";
						winz2 = "+" + lastRolls['roll2']['reward'];
					}
					else {
						winz = "<h1 id=\"under-lose\">";
						winz2 = "-" + lastRolls['roll2']['bet'];
					}
					
					lastRollsDiv = "<div id=\"lastRoll\"><center>" + winz + "<" + lastRolls['roll2']['rollUnder'] + " | x" + lastRolls['roll2']['multiplier'] + " | " + winz2 + " SBD | " + lastRolls['roll2']['pick'] + "</h3></center></div>" + lastRollsDiv;
				}
				
				if(lastRolls['roll1']) {
					if(lastRolls['roll1']['win']) {
						winz = "<h1 id=\"under-win\">";
						winz2 = "+" + lastRolls['roll1']['reward'];
					}
					else {
						winz = "<h1 id=\"under-lose\">";
						winz2 = "-" + lastRolls['roll1']['bet'];
					}
					
					lastRollsDiv = "<div id=\"lastRoll\"><center>" + winz + "<" + lastRolls['roll1']['rollUnder'] + " | x" + lastRolls['roll1']['multiplier'] + " | " + winz2 + " SBD | " + lastRolls['roll1']['pick'] + "</h3></center></div>" + lastRollsDiv;
				}

				
				$("#lastRolls").html(lastRollsDiv);
			}
		</script>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div id="dicesBody">
			<center><h1 style="margin-bot:0">Dices</h1></center>
			<div id="dicesGame">
				<b style="float:left;margin-left:10px;margin-top:10px;">Bet Amount</b>
				<b style="float:right;margin-right:10px;margin-top:10px;">Profit On Win</b><br><br>
				<input id="dicesInput" style="margin-left:10px;" type="number" step=".001" min="0.001" value="1.000" pattern="\d+(\.\d{2})?" name="bet">
				<button class="dicesButton" id="dicesx2" onClick="doubleDices();">x2</button>
				<button class="dicesButton" id="dices12" onClick="halfDices();">1/2</button>
				<b style="float:right;margin-right:10px;font-size:40px;color:white" id="profit">2.000</b><br><br>
				<b style="margin-left:10px;">Roll </b><b style="color:#00c74d">under</b>
				<b style="margin-left:14%">Multiplier</b><br><br>
				<input style="margin-left:10px;" type="number" class="dicesChances" id="rollUnder" value="4750" min="10" max="9400">
				<input style="margin-left:10px;" type="number" class="dicesChances" id="multiplier" value="2.00" min="1" max="9500"><br><br>
				<button class="rollButton" id="rollButton" onclick="rollDices();">ROLL!</button><br><br><br>
				
				<div style="margin-left:10%;width:80%;height:40%" id="lastRolls">
					
				</div>
				
			</div>
			<center>Player secret: <input type="text" id="secretInput"></center>
			<center><p id="diceshash"></p></center>
			<center><p id="currSecret"></p></center>
		</div>
		<?php include('src/footer.php'); ?>
	</body>
</html>