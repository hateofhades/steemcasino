<html>
	<head>
		<?php include('src/head.php'); ?>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div class="games-body">
			<center><h1 style="display:inline">Rock, Paper, Scissors </h1><b><a href="games.php" style="display:inline;text-decoration:none;color:black;">(Go back)</a></b></center>
			<center><a href="#" id="newGameBttn" style="text-decoration:none;color:black;font-size:24px" onClick="startGame();">Start a new game </a> <p id="llll" style="display:inline">|</p> <a href="#" id="gamesEnded" style="text-decoration:none;color:black;font-size:24px" onClick="past(1);">See games that ended</a></center>
			<div class="coinflip-games">
				<iframe id="coinflip-iframe" width="100%" scrolling="no" style="overflow:hidden;" height="22%" frameborder="0" src="rpsgames.php">
					Sorry, but your browser is not supported. Please upgrade your browser!
				</iframe>
			</div>
			<div class="coinflip-game" style="height:60%">
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
					$("#coinflip-iframe").attr("src", "rpsgames.php");
				else
					$("#coinflip-iframe").attr("src", "rpsgames.php?past=1");
				setTimeout(function() { reloadIFrame(); }, 30000);
			}
			
			function startGame() {
				$("#iframe").attr("src", "rpsa.php?action=newgame");
				$(".coinflip-game").show();
			}
			
			function past(pastor) {
				if(pastor == 0) {
					$("#coinflip-iframe").attr("src", "rpsgames.php");
					$("#gamesEnded").text("See games that ended");
					$("#gamesEnded").attr("onClick", "past(1);");
					$("#newGameBttn").show();
					$("#llll").show();
					pasted = 0;
				}
				else {
					$("#coinflip-iframe").attr("src", "rpsgames.php?past=1");
					$("#gamesEnded").text("See avalabile games");
					$("#gamesEnded").attr("onClick", "past(0);");
					$("#newGameBttn").hide();
					$("#llll").hide();
					$(".coinflip-game").hide();
					$("#iframe").attr("src", "");
					pasted = 1;
				}
			}
			
			function enterGame(gameId) {
				$("#iframe").attr("src", "confrps.php?game=" + gameId);
				$(".coinflip-game").show();
			}
			
			function viewGame(gameid, player1, player2, bet, reward, player1pick, player2pick, win) {
				$("#iframe").attr("src", "");
				$(".coinflip-game").hide();
				$("#iframe").attr("src", "viewrps.php?gameid=" + gameid + "&player1=" + player1 + "&player2=" + player2 + "&bet=" + bet + "&reward=" + reward + "&player1pick=" + player1pick + "&player2pick=" + player2pick + "&win=" + win);
				$(".coinflip-game").show();
			}
		</script>
		<?php include('src/footer.php'); ?>
	</body>
</html>