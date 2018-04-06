<?php
$gameid = $_GET['gameid'];
$player1 = $_GET['player1'];
$player2 = $_GET['player2'];
$bet = $_GET['bet'];
$reward = $_GET['reward'];
$hash = $_GET['hash'];
$secret = $_GET['secret'];

if($secret[0] == "A") {
	$winner = $player1;
	$animation = 1;
}
else {
	$winner = $player2;
	$animation = 2;
}

if($animation == 1)
	$animation = "<img style=\"width:50%\" id=\"gif\" src=\"img/animation1.gif?updated=".time()."\">";
else if($animation == 2)
	$animation = "<img style=\"width:50%\" id=\"gif\" src=\"img/animation2.gif?updated=".time()."\">";
						
						
?>
<html>
	<head>
		<title>Game number - <?php echo $gameid;?></title>
		<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
	</head>
	<body>
		<center><h3 style="margin:0"><?php echo $player1." VS. ".$player2;?></h3><?php echo $animation;?><br><h3 id="winner"></h3><h4 id="win" style="margin:0"></h1><a style="text-decoration:underline;cursor:pointer" id="close" onclick="parent.$('.coinflip-game').hide();parent.$('#iframe').attr('src', '');"></a></center>
		
		<script>
			function winner() {
				$('#winner').text("Winner: <?php echo $winner;?>");
				$('#win').text("Congratulations! You have won: <?php echo $reward; ?> SBD.");
				$('#close').text("Close");
			}
		
			setTimeout(function(){
				winner();
			}, 5000);
		</script>
	</body>
</html>
