<?php
include_once('src/db.php');

$body = "";

$win = 0;

$past = 0;

$query = $db->prepare('SELECT * FROM rps');
						
$query->execute();
$result = $query->get_result();
if(!isset($_GET['past'])) {
	if($result->num_rows) {
		while ($row = $result->fetch_assoc()) { 
			$timestamp = $row['timestamp'];
			$timestamper = time() - $timestamp;
			if($timestamp == 0) {
				$gameid = $row['ID'];
				$player1 = $row['player1'];
				$player2 = $row['player2'];
				$bet = $row['bet'];
				$reward = $row['reward'];
				if($player1 == "")
					$players = "<a href=\"#\" onClick=\"MyWindow=window.open('confrps.php?game=".$gameid."','MyWindow',width=600,height=300); return false;\">Enter game</a><br>".$player2;
				else
					$players = "".$player1."<br><a href=\"#\" onClick=\"MyWindow=window.open('confrps.php?game=".$gameid."','MyWindow',width=600,height=300); return false;\">Enter game</a>";
				
				$body .= "
				<div style=\"display:inline;float:left;padding-left:10px\"><center>
					<h3>Game #".$gameid."</h3>
					<h4>Players<br>".$players."<br><br>
					Bet: ".$bet." SBD </center>
				</div>
				";
			} else if($timestamper <= 60) {
				$gameid = $row['ID'];
				$player1 = $row['player1'];
				$player2 = $row['player2'];
				$bet = $row['bet'];
				$reward = $row['reward'];
				$player1pick = $row['player1pick'];
				$player2pick = $row['player2pick'];
				
				$win = $row['win'];
				
				if($win == 1)
					$winner = $player1;
				else
					$winner = $player2;
				
					$players = $player1."<br>".$player2."<br><a href=\"#\" onClick=\"MyWindow=window.open('viewrps.php?gameid=".$gameid."&player1=".$player1."&player2=".$player2."&bet=".$bet."&reward=".$reward."&player1pick=".$player1pick."&player2pick=".$player2pick."&win=".$win."','MyWindow',width=600,height=300); return false;\">View game</a>";
				
				$body .= "
				<div style=\"display:inline;float:left;padding-left:10px;\"><center>
					<h3>Game #".$gameid."</h3>
					<h4>Players<br>".$players."<br>
					Jackpot: ".$reward." SBD
					</center>
				</div>
				";
			}
		}
	} else {
		$body = "<br><center><h1 style=\"color:red\">No game history avalabile.</h1></center>";
	}
} else {
	$past=1;
	if($result->num_rows) {
		if($result->num_rows) {
			while ($row = $result->fetch_assoc()) {
				$gameid = $row['ID'];
				$player1 = $row['player1'];
				$player2 = $row['player2'];
				$bet = $row['bet'];
				$reward = $row['reward'];
				$player1pick = $row['player1pick'];
				$player2pick = $row['player2pick'];
				
				$win = $row['win'];
				
				if($win == 0)
					continue;
				
				if($win == 1)
					$winner = $player1;
				else if($win == 2)
					$winner = $player2;
				else
					$winner = "Draw";
				
				if($winner != "Draw") {
					$winner = "Winner: ".$winner;
				}
				
					$players = $player1."<br>".$player2."<br><a href=\"#\" onClick=\"MyWindow=window.open('viewrps.php?gameid=".$gameid."&player1=".$player1."&player2=".$player2."&bet=".$bet."&reward=".$reward."&player1pick=".$player1pick."&player2pick=".$player2pick."&win=".$win."','MyWindow',width=600,height=300); return false;\">View game</a>";
				
				$body = "
				<div style=\"display:inline;float:left;padding-left:10px;\"><center>
					<h3>Game #".$gameid."</h3>
					<h4>Players<br>".$players."<br>
					Jackpot: ".$reward." SBD
					<br>".$winner."</center>
				</div>".$body;
			}
		}
	} else {
		$body = "<br><center><h1 style=\"color:red\">No game history avalabile.</h1></center>";
	}
}	
	
$page = $_SERVER['PHP_SELF'];
$secrefresh = "30";
?>

<html>
	<head>
		<?php include('src/head.php'); ?>
		<meta http-equiv="refresh" content="<?php echo $secrefresh?>;URL='<?php echo $page?><?php if($past == 1) echo "?past=1";?>'">
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div class="games-body">
			<center><h1 style="display:inline">Rock, Paper, Scissors </h1><b><a href="games.php" style="display:inline;text-decoration:none;color:black;">(Go back)</a></b></center>
			<center><a href="#" style="text-decoration:none;color:black;font-size:24px" onClick="MyWindow=window.open('rpsa.php?action=newgame','MyWindow',width=600,height=300); return false;">Start a new game </a> | <a href="<?php if($past == 1) echo "rps.php"; else echo "?past=1";?>" style="text-decoration:none;color:black;font-size:20px"> <?php if($past == 1) echo "See avalabile games"; else echo "See games that ended"; ?></a></center>
			<?php echo $body; ?>
		</div>
		<?php include('src/footer.php'); ?>
	</body>
</html>