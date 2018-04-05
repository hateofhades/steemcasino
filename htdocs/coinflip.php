<html>
	<head>
		<?php include('src/head.php'); ?>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div class="games-body">
			<center><h1 style="display:inline">Coinflip </h1><b><a href="games.php" style="display:inline;text-decoration:none;color:black;">(Go back)</a></b></center>
			<center><a href="#" id="newGameBttn" style="text-decoration:none;color:black;font-size:24px" onClick="startGame();">Start a new game </a> <p id="llll" style="display:inline">|</p> <a href="#" id="gamesEnded" style="text-decoration:none;color:black;font-size:24px" onClick="past(1);">See games that ended</a></center>
			<div class="coinflip-games">
				<iframe id="coinflip-iframe" width="100%" scrolling="no" style="overflow:hidden;" height="30%" frameborder="0" src="coinflipgames.php">
					Sorry, but your browser is not supported. Please upgrade your browser!
				</iframe>
			</div>
			<div class="coinflip-game">
				<iframe id="iframe" width="100%" scrolling="no" style="overflow:hidden;" height="100%" frameborder="0">
					Sorry, but your browser is not supported. Please upgrade your browser!
				</iframe>
			</div>
		</div>
		<script>	
		var pasted = 0;
			$(document).ready(function() {
				setTimeout(function() { reloadIFrame(); }, 30000);
			});
			
			function reloadIFrame () {
				if(pasted == 0)
					$("#coinflip-iframe").attr("src", "coinflipgames.php");
				else
					$("#coinflip-iframe").attr("src", "coinflipgames.php?past=1");
				setTimeout(function() { reloadIFrame(); }, 30000);
			}
			
			function startGame() {
				$("#iframe").attr("src", "coinflipaction.php?action=newgame");
				$(".coinflip-game").show();
			}
			
			function enterGame(gameId) {
				$("#iframe").attr("src", "confirmcoinflip.php?game=" + gameId);
				$(".coinflip-game").show();
			}
			
			function viewGame(gameid, player1, player2, bet, reward, hash, secret) {
				$("#iframe").attr("src", "");
				$(".coinflip-game").hide();
				$("#iframe").attr("src", "viewcoinflipgame.php?gameid=" + gameid + "&player1=" + player1 + "&player2=" + player2 + "&bet=" + bet + "&reward=" + reward + "&hash=" + hash + "&secret=" + secret);
				$(".coinflip-game").show();
			}
			
			function past(pastor) {
				if(pastor == 0) {
					$("#coinflip-iframe").attr("src", "coinflipgames.php");
					$("#gamesEnded").text("See games that ended");
					$("#gamesEnded").attr("onClick", "past(1);");
					$("#newGameBttn").show();
					$("#llll").show();
					pasted = 0;
				}
				else {
					$("#coinflip-iframe").attr("src", "coinflipgames.php?past=1");
					$("#gamesEnded").text("See avalabile games");
					$("#gamesEnded").attr("onClick", "past(0);");
					$("#newGameBttn").hide();
					$("#llll").hide();
					$(".coinflip-game").hide();
					$("#iframe").attr("src", "");
					pasted = 1;
				}
			}
		</script>
		<?php include('src/footer.php'); ?>
	</body>
</html>