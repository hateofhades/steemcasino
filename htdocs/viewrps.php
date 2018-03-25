<?php
$game = $_GET['gameid'];
$player1 = $_GET['player1'];
$player2 = $_GET['player2'];
$bet = $_GET['bet'];
$reward = $_GET['reward'];
$player1p = $_GET['player1pick'];
$player2p = $_GET['player2pick'];

if($player1p == 1)
	$player1p = "Rock";
else if($player1p == 2)
	$player1p = "Paper";
else
	$player1p = "Scissors";

if($player2p == 1)
	$player2p = "Rock";
else if($player2p == 2)
	$player2p = "Paper";
else
	$player2p = "Scissors";

if($player1p == $player2p)
	$win = "Draw";
else if(($player1p == 1 && $player2p == 3) || ($player1p == 2 && $player2p == 1) || ($player1p == 3 && $player2p == 2))
	$win = $player1." has won.";
else
	$win = $player2." has won.";

?>
<html style="font-family: Arial">
	<head>
		<title>SteemCasino</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
		<script src="dist/jquery.countdown360.min.js"></script>
	</head>
	<body style="background-color:#0276FD">
		<center>
			<h2><?php echo $player1." VS. ".$player2; ?></h2>
			<div id="countdown"></div>
			<h3 id="player1"></h3>
			<h3 id="player2"></h3>
			<br>
			<h4 id="win"></h4>
			
			<script>		
			$("#countdown").countdown360({
			radius      : 60.5,
			seconds     : 5,
			strokeWidth : 15,
			fillStyle   : '#0276FD',
			strokeStyle : '#003F87',
			fontSize    : 50,
			fontColor   : '#FFFFFF',
			autostart: false,
			onComplete  : function () { setTimeout(function() { winner(); }, 1000); }
			}).start();
			
			function winner () {
				$("#countdown").remove();
				$("#player1").text("<?php echo $player1." choose: ".$player1p;?>");
				$("#player2").text("<?php echo $player2." choose: ".$player2p;?>");
				$("#win").text("<?php echo $win;?>");
			}
			</script>
		</center>
	</body>
</html>