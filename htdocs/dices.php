<html>
	<head>
		<?php include('src/head.php'); ?>
		<script>
			var animateVar = 0, setRollBackTimeout, rolling = 0;
			
			function doubleDices() {
				var currValue = $("#dicesInput").val();
				currValue = currValue * 2;
				$("#dicesInput").val(currValue);
				
				var value = $("#multiplier").val();
					
				$("#profit").text((currValue * value).toFixed(3));
			}
			
			function rollDices() {
				var bet = $("#dicesInput").val();
				var under = $("#rollUnder").val();
				
				if(!rolling) {
					rolling = 1;
					$.getJSON( "../src/dices.php?bet=" + bet + "&under=" + under, function( data ) {
						console.log(data);
						if(data['status'] == 'success') {
							clearTimeout(setRollBackTimeout);
							animateVar = 0;
							animate(data['pick'], data['win'], data['balance']);
						} else {
							rolling = 0;
						}
					});
				}
			}
			
			function animate(pick, win, bal) {
				animateVar++;
				picker = Math.floor(Math.random() * 10000);
				$("#rollButton").text(picker);
				if(animateVar <= 30)
				setTimeout(function () { animate(pick, win, bal) }, 100);
				else {
					rolling = 0;
					$("#rollButton").text(pick);
					
					if(win) 
						$("#rollButton").css("color", "green");
					else
						$("#rollButton").css("color", "red");
					
					setRollBackTimeout = setTimeout(function () { setRollBack(bal) }, 5000);
				}
			}
			
			function setRollBack (bal) {
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
				$('#rollUnder').on('input', function() {
					var value = $("#rollUnder").val();
						//value.toFixed(0);
					
					if(value < 10)
						$("#rollUnder").val(10);
					else if(value > 9400)
						$("#rollUnder").val(9400);
					
					$("#multiplier").val((9500/value).toFixed(2));
					
					var bet = $("#dicesInput").val();
					
					$("#profit").text((bet * (9500/value)).toFixed(3));
				});
				
				$('#multiplier').on('input', function() {
					var value = $("#multiplier").val();
						//value.toFixed(2);
					
					if(value < 1)
						$("#multiplier").val(1);
					else if(value > 9500)
						$("#multiplier").val(9500);
					
					$("#rollUnder").val((9500/value).toFixed(0));
					
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
				<button class="rollButton" id="rollButton" onclick="rollDices();">ROLL!</button>
			</div>
		</div>
		<?php include('src/footer.php'); ?>
	</body>
</html>