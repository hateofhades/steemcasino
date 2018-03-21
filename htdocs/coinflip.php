<?php
include_once('src/db.php');

$win = 0;

$query = $db->prepare('SELECT * FROM coinflip');
						
$query->execute();
$result = $query->get_result();
if($result->num_rows) {
	$body = "";
	while ($row = $result->fetch_assoc()) { 
		$timestamp = $row['timestamp'];
		$timestamper = time() - $timestamp;
		if($timestamp == 0) {
			$gameid = $row['ID'];
			$player1 = $row['player1'];
			$player2 = $row['player2'];
			$bet = $row['bet'];
			$reward = $row['reward'];
			$hash = $row['hash'];
			
			if($player1 == "")
				$players = "Steem - <a href=\"#\" onClick=\"MyWindow=window.open('confirmcoinflip.php?game=".$gameid."','MyWindow',width=600,height=300); return false;\">Enter game</a><br>Bitcoin - ".$player2;
			else if($player2 == "")
				$players = "Steem - ".$player1."<br>Bitcoin - <a href=\"#\" onClick=\"MyWindow=window.open('confirmcoinflip.php?game=".$gameid."','MyWindow',width=600,height=300); return false;\">Enter game</a>";
			else 
				$players = $player1."<br>".$player2."<br><a href=\"#\">View game</a>";
			
			$body .= "
			<div style=\"display:inline;float:left;padding-left:10px\"><center>
				<h3>Game #".$gameid."</h3>
				<h4>Players<br>".$players."<br><br>
				Bet: ".$bet." SBD <br><br><a href=\"#\" onClick=\"MyWindow=window.open('hash.php?hash=".$hash."','MyWindow',width=600,height=300); return false;\">
				See hash
				</a></center>
			</div>
			";
		} else if($timestamper <= 60) {
			$gameid = $row['ID'];
			$player1 = $row['player1'];
			$player2 = $row['player2'];
			$bet = $row['bet'];
			$reward = $row['reward'];
			$hash = $row['hash'];
			$secret = $row['secret'];
			
			$win = $row['win'];
			
			if($win == 1)
				$winner = $player1;
			else
				$winner = $player2;
			
				$players = $player1."<br>".$player2."<br><a href=\"#\">View game</a>";
			
			$body .= "
			<div style=\"display:inline;float:left;padding-left:10px;\"><center>
				<h3>Game #".$gameid."</h3>
				<h4>Players<br>".$players."<br>
				Jackpot: ".$reward." SBD
				<br>Winner: ".$winner."<br><a href=\"#\" onClick=\"MyWindow=window.open('hash.php?hash=".$hash."&secret=".$secret."','MyWindow',width=600,height=300); return false;\">
				See hash and secret
				</a></center>
			</div>
			";
		}
	}
} else {
	$body = "<br><center><h1 style=\"color:red\">No games avalabile.</h1></center>";
}

$page = $_SERVER['PHP_SELF'];
$secrefresh = "30";
?>

<html>
	<head>
		<?php include('src/head.php'); ?>
		<meta http-equiv="refresh" content="<?php echo $secrefresh?>;URL='<?php echo $page?>'">
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div>
			<center><h1 style="display:inline">Coinflip </h1><b><a href="games.php" style="display:inline;text-decoration:none;color:black;">(Go back)</a></b></center>
			<center><a href="#" style="text-decoration:none;color:black;font-size:24px" onClick="MyWindow=window.open('coinflipaction.php?action=newgame','MyWindow',width=600,height=300); return false;">Start new game</a></center>
			<?php echo $body; ?>
		</div>
	</body>
</html>