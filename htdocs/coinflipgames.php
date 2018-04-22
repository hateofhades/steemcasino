<?php
include_once('src/db.php');

$body = "";

$win = 0;

$past = 0;

$query = $db->prepare('SELECT * FROM coinflip');
						
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
				$hash = $row['hash'];
				
				if($player1 == "")
					$players = "Steem - <a style=\"text-decoration:underline;cursor:pointer\" onClick=\"parent.enterGame(".$gameid.");\">Enter game</a><br>Bitcoin - ".$player2;
				else
					$players = "Steem - ".$player1."<br>Bitcoin - <a style=\"text-decoration:underline;cursor:pointer\" onClick=\"parent.enterGame(".$gameid.");\">Enter game</a>";
				
				if($player1 == $_COOKIE['username'] || $player2 == $_COOKIE['username'])
					$cancel = "<br><a style=\"text-decoration:underline;cursor:pointer\" onclick=\"parent.cancelGame(".$gameid.")\">Cancel game</a>";
				else
					$cancel = "";
				
				$body .= "
				<div style=\"display:inline;float:left;padding-left:10px;border:1px solid black;margin:4px\">
					<center><h3>Game #".$gameid."</h3></center>
					<center><h4 style=\"display:inline\">Players</h4></center><h4 style=\"display:inline\">".$players."</h4><center><h4 style=\"display:inline\">
					Bet: ".$bet." SBD <br><br><a style=\"text-decoration:underline;cursor:pointer\" onClick=\"parent.$('.coinflip-game').show();parent.$('#iframe').attr('src', 'hash.php?hash=".$hash."');\">
					See hash
					</a>".$cancel."
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
				
					$players = $player1."<br>".$player2."<br><a style=\"text-decoration:underline;cursor:pointer\" onClick=\"parent.viewGame(".$gameid.", '".$player1."', '".$player2."', ".$bet.", ".$reward.", '".$hash."', '".$secret."');\">View game</a>";
				
				$body .= "
				<div style=\"display:inline;float:left;padding-left:10px;border:1px solid black;margin:4px\">
					<center><h3>Game #".$gameid."</h3></center>
					<center><h4>Players<br>".$players."<br>
					Jackpot: ".$reward." SBD
					<br><br><a style=\"text-decoration:underline;cursor:pointer\" onClick=\"parent.$('.coinflip-game').show();parent.$('#iframe').attr('src', 'hash.php?hash=".$hash."&secret=".$secret."');\">
					See hash and secret
					</a></center>
				</div>
				";
			}
		}
	} else {
		$body = "<br><center><h1 style=\"color:red\">No games avalabile.</h1></center>";
	}
}
else {
	$past=1;
	if($result->num_rows) {
		if($result->num_rows) {
			while ($row = $result->fetch_assoc()) {
				$gameid = $row['ID'];
				$player1 = $row['player1'];
				$player2 = $row['player2'];
				$bet = $row['bet'];
				$reward = $row['reward'];
				$hash = $row['hash'];
				$secret = $row['secret'];
				
				$win = $row['win'];
				
				if($win == 0)
					continue;
				
				if($win == 1)
					$winner = $player1;
				else
					$winner = $player2;
				
					$players = $player1."<br>".$player2."<br><a style=\"text-decoration:underline;cursor:pointer\" onClick=\"parent.viewGame(".$gameid.", '".$player1."', '".$player2."', ".$bet.", ".$reward.", '".$hash."', '".$secret."');\">View game</a>";
				
				$body = "
				<div style=\"display:inline;float:left;padding-left:10px;border: 1px solid black;margin:4px;\">
					<center><h3>Game #".$gameid."</h3></center>
					<center><h4 style=\"display:inline\">Players<br>".$players."<br>
					Jackpot: ".$reward." SBD</h4></center><h4 style=\"display:inline\">
					<br>Winner: ".$winner."<br><a style=\"text-decoration:underline;cursor:pointer\" onClick=\"parent.$('.coinflip-game').show();parent.$('#iframe').attr('src', 'hash.php?hash=".$hash."&secret=".$secret."');\">
					See hash and secret
					</a></h4><br><br>
				</div>".$body;
			}
		}
	}
	else {
		$body = "<br><center><h1 style=\"color:red\">No game history avalabile.</h1></center>";
	}
}	
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