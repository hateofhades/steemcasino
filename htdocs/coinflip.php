<?php


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
		</div>
	</body>
</html>