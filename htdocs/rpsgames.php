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
					$players = "<a style=\"text-decoration:underline;cursor:pointer\" onClick=\"parent.enterGame(".$gameid.");\">Enter game</a><br>".$player2;
				else
					$players = "".$player1."<br><a style=\"text-decoration:underline;cursor:pointer\" onClick=\"parent.enterGame(".$gameid.");\">Enter game</a>";
				
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
				
					$players = $player1."<br>".$player2."<br><a style=\"text-decoration:underline;cursor:pointer\" onClick=\"parent.viewGame(".$gameid.", '".$player1."', '".$player2."', ".$bet.", ".$reward.", ".$player1pick.", ".$player2pick.", ".$win.");\">View game</a>";
				
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
				
					$players = $player1."<br>".$player2."<br><a style=\"text-decoration:underline;cursor:pointer\" onClick=\"parent.viewGame(".$gameid.", '".$player1."', '".$player2."', ".$bet.", ".$reward.", ".$player1pick.", ".$player2pick.", ".$win.");\">View game</a>";
				
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
		<link rel="stylesheet" href="dist/owl.carousel.min.css">
		<?php include('src/head.php'); ?>
		<script src="dist/owl.carousel.min.js"></script>
	</head>
	<body>
			<div class="owl-carousel">
				<?php echo $body; ?>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				var owl = $('.owl-carousel');
				owl.owlCarousel({
					loop: false,
					margin:0,
					autoWidth:true,
				}); 
			});
		</script>
	</body>
</html>