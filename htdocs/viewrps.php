<?php
$game = $_GET['gameid'];
$player1 = $_GET['player1'];
$player2 = $_GET['player2'];
$bet = $_GET['bet'];
$reward = $_GET['reward'];
$player1p = $_GET['player1pick'];
$player2p = $_GET['player2pick'];
$win = $_GET['win'];

if($player1p == 1)
	$player1p = "<img style=\"width:30%;float:left\" id=\"rps1\" src=\"img/rock.png\">";
else if($player1p == 2)
	$player1p = "<img style=\"width:30%;float:left\" id=\"rps1\" src=\"img/paper.png\">";
else
	$player1p = "<img style=\"width:30%;float:left\" id=\"rps1\" src=\"img/scissors.png\">";

if($player2p == 1)
	$player2p = "<img style=\"width:30%;float:right\" id=\"rps2\" src=\"img/rock.png\">";
else if($player2p == 2)
	$player2p = "<img style=\"width:30%;float:right\" id=\"rps2\" src=\"img/paper.png\">";
else
	$player2p = "<img style=\"width:30%;float:right\" id=\"rps2\" src=\"img/scissors.png\">";

if($win == 3)
	$win = "Draw";
else if($win == 1)
	$win = $player1." has won ".$reward." SBD.";
else
	$win = $player2." has won ".$reward." SBD.";

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
			<h3 id="player1" style="float:left; display:inline;margin-left:5%"></h3>
			<h3 id="player2" style="float:right; display:inline;margin-right:5%"></h3><br><br><br><br>
			<?php echo $player1p.$player2p;?>
			<br><br><br>
			<center><h4 id="win"></h4></center>
			
			<script>	
			$("#rps1").hide();
			$("#rps2").hide();
			
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
				$("#rps1").show();
				$("#rps2").show();
				$("#player1").text('<?php echo $player1;?>');
				$("#player2").text('<?php echo $player2;?>');
				$("#win").text("<?php echo $win;?>");
			}
			</script>
		</center>
	</body>
</html>