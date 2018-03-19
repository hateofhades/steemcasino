<?php
include_once('src/db.php');

$win = 0;

$query = $db->prepare('SELECT * FROM coinflip WHERE win = ?');
$query->bind_param('i', $win);
						
$query->execute();
$result = $query->get_result();
if($result->num_rows) {
	$body = "";
	while ($row = $result->fetch_assoc()) { 
		$gameid = $row['ID'];
		$player1 = $row['player1'];
		$player2 = $row['player2'];
		$bet = $row['bet'];
		$reward = $row['reward'];
		$hash = $row['hash'];
		
		if($player1 == "")
			$players = "<a href=\"#\">Enter</a><br>".$player2;
		else 
			$players = $player1."<br><a href=\"#\">Enter</a>";
		
		$body .= "
		<div>
			<h1>Game ID:".$gameid."</h1>
			<h3>Players<br>".$players."<br><br>
			Bet: ".$bet." SBD (Reward:".$reward." SBD)<br><a href=\"#\" onClick=\"MyWindow=window.open('hash.php?hash=".$hash."','MyWindow',width=600,height=300); return false;\">
			See hash
			</a>
		</div>
		";
	}
} else {
	$body = "<h1 style=\"color:red\">No games avalabile.</h1>";
}
?>

<html>
	<head>
		<?php include('src/head.php'); ?>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div>
			<center><h1 style="display:inline">Coinflip </h1><b><a href="games.php" style="display:inline;text-decoration:none;color:black;">(Go back)</a></b></center>
			<center><a href="#" style="text-decoration:none;color:black;font-size:24px" onClick="MyWindow=window.open('coinflipaction.php?action=newgame','MyWindow',width=600,height=300); return false;">Start new game</a></center>
			<center><?php echo $body; ?></center>
		</div>
	</body>
</html>