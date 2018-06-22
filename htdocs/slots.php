<html>
	<head>
		<?php include('src/head.php'); ?>
		<link rel="stylesheet" href="src/slots.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="dist/jquery.slotmachine.min.css" type="text/css" media="screen" />
		<script type="text/javascript" src="dist/slotmachine.js"></script>
		<script src="dist/jquery.slotmachine.min.js"></script>
		<script>
		var slot1, slot2, slot3, running = 0, slotwin, slotstart;
		$(document).ready(function(){
			$("#secretInput").val(Math.random().toString(36).replace(/[^a-z123456789]+/g, '').substr(0, 10));
			slotwin = document.createElement('audio');
			slotstart = document.createElement('audio');
			
			slotwin.setAttribute('src', '/audio/slot_win.wav');
			slotstart.setAttribute('src', '/audio/spin.mp3');
			
			slot1 = $("#casino1").slotMachine({
				active	: 2,
				delay	: 500
			});
			slot2 = $("#casino2").slotMachine({
				active	: 3,
				delay	: 500
			});
			slot3 = $("#casino3").slotMachine({
				active	: 0,
				delay	: 500
			});
		});
		
		function spin() {
			var bet = $("#bet").val();
			var secret = $("#secretInput").val();
			if(!running) {
				$.getJSON( "/src/slots.php?bet=" + bet + "&secret=" + secret , function( data ) {
					console.log(data);
					if(data['status'] == "success") {
						running = 1;
						
						slotwin.currentTime = 0;
						slotstart.play();
						
						unsetWin();
						
						console.log("Spinning...");
						$("#currSecret").text("Last roll secret: " + data['secret']);
						$("#diceshash").text("Current hash: " + data['hash']);
						animateSlots(data['slot1'], data['slot2'], data['slot3']);
						$("#balance").text("Balance: " + data['balance'] + " SBD");
						setTimeout(function() {winSet(data['win']);}, 12000);
					}
				});
			}
		}
		
		function unsetWin() {
			$("#2x").css("background-color", "");
			$("#2.5x").css("background-color", "");
			$("#3x").css("background-color", "");
			$("#4x").css("background-color", "");
			$("#5.5x").css("background-color", "");
			$("#7x").css("background-color", "");
		}
		
		function winSet(winId) {
			running = 0;
			
			if(winId) {
				slotwin.play();
				if(winId == 1)
					$("#2x").css("background-color", "green");
				else if(winId == 2)
					$("#2.5x").css("background-color", "green");
				else if(winId == 3)
					$("#3x").css("background-color", "green");
				else if(winId == 4)
					$("#4x").css("background-color", "green");
				else if(winId == 5)
					$("#5.5x").css("background-color", "green");
				else if(winId == 6)
					$("#7x").css("background-color", "green");
			}
		}
		
		function animateSlots(sslot1, sslot2, sslot3) {
			slot1.randomize = sslot1;
			slot2.randomize = sslot2;
			slot3.randomize = sslot3;
			slot1.shuffle(20);
			slot2.shuffle(25);
			slot3.shuffle(30);
		}
		</script>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div>
			<div id="casino" style="padding-top:50px;">
			  <div class="content" style="height:84%">
				<h1>Slots</h1>

				<div>
				  <div id="casino1" class="slotMachine" style="margin-left: -65px">
					<div class="slot slot1"></div>
					<div class="slot slot2"></div>
					<div class="slot slot3"></div>
					<div class="slot slot4"></div>
					<div class="slot slot5"></div>
					<div class="slot slot6"></div>
				  </div>

				  <div id="casino2" class="slotMachine">
					<div class="slot slot1"></div>
					<div class="slot slot2"></div>
					<div class="slot slot3"></div>
					<div class="slot slot4"></div>
					<div class="slot slot5"></div>
					<div class="slot slot6"></div>
				  </div>

				  <div id="casino3" class="slotMachine">
					<div class="slot slot1"></div>
					<div class="slot slot2"></div>
					<div class="slot slot3"></div>
					<div class="slot slot4"></div>
					<div class="slot slot5"></div>
					<div class="slot slot6"></div>
				  </div>
				  
				  <div id="slotsInput">
					<center>
						<span id="betn">Bet :</span><input type="number" step=".001" min="0.001" value="1" pattern="\d+(\.\d{2})?" id="bet" name="bet"><br>
						<button onClick="spin()">Spin</button>
					</center>
				  </div>
				  
				  <div id="payoutTable">
					<div id="2x">
						<img src="img/slot1.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot2.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot3.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<h5 style="display:inline"> - 2x</h5><br>
					</div>
					<div id="2.5x">
						<img src="img/slot1.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot1.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot1.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<h5 style="display:inline"> - 2.5x</h5><br>
						<img src="img/slot2.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot2.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot2.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<h5 style="display:inline"> - 2.5x</h5><br>
						<img src="img/slot3.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot3.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot3.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<h5 style="display:inline"> - 2.5x</h5><br>
					</div>
					<div id="3x">
						<img src="img/slot4.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot4.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot4.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<h5 style="display:inline"> - 3x</h5><br>
					</div>
					<div id="4x">
						<img src="img/slot4.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot5.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot6.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<h5 style="display:inline"> - 4x</h5><br>
					</div>
					<div id="5.5x">
						<img src="img/slot5.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot5.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot5.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<h5 style="display:inline"> - 5.5x</h5><br>
					</div>
					<div id="7x">
						<img src="img/slot6.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot6.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<img src="img/slot6.png" style="height:50px;width:50px;display:inline;vertical-align:middle">
						<h5 style="display:inline"> - 7x</h5><br>
					</div>
				  </div>
			</div>
			</div>
		</div>
		<center>Player secret: <input type="text" id="secretInput"></center>
		<center><p id="slotshash"></p></center>
		<center><p id="currSecret"></p></center>
		</div>
		<?php include('src/footer.php'); ?>
	</body>
</html>>