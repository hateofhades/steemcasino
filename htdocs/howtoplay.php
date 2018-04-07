<html>
	<head>
		<?php include('src/head.php'); ?>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		
			<br>
			<center>
				<a onclick="show(1)" style="display:inline;text-decoration:underline;cursor:pointer">Coinflip </a>|
				<a onclick="show(2)" style="display:inline;text-decoration:underline;cursor:pointer"> Rock Paper Scissors </a>|
				<a onclick="show(3)" style="display:inline;text-decoration:underline;cursor:pointer"> Mines </a>|
				<a onclick="show(4)" style="display:inline;text-decoration:underline;cursor:pointer"> Roulette</a>
			</center><br>
			<div id="con" style="display:none">
				<center>
					<h3 style="display:inline">1. Create a game using the "Start a new game" button.</h3><br>
					<img src="https://i.imgur.com/h5YCT0e.png">
					<br><br>
					<h3 style="display:inline">2. Select how much you want to bet and on the which side and then press submit.</h3><br>
					<p style="display:inline">(For example I will bet on Bitcoin 2 SBD)</p><br>
					<img src="https://i.imgur.com/ZX3Bp3j.png">
					<br><br>
					<h3 style="display:inline">Now a game has been created, now you will have to wait for somebody to enter your game.</h3><br>
					<img src="https://i.imgur.com/tI4ux1g.png">
					<br><br>
					<h3 style="display:inline">If you want to enter in a coinflip game, just press the Enter game button!</h3><br>
					<img src="https://i.imgur.com/4fMO0B0.png">
				</center>
			</div>
			<div id="rpss" style="display:none">
				<center>
					<h3 style="display:inline">1. Create a game using the "Start a new game" button.</h3><br>
					<img src="https://i.imgur.com/mvmG936.png"><br><br>
					<h3 style="display:inline">2. Select how much you want to bet and what is your pick.</h3><br>
					<p style="display:inline">(For example I will bet 2 SBD and pick Paper)</p><br>
					<img src="https://i.imgur.com/4PJ9APE.png"><br><br>
					<h3 style="display:inline">Now a game has been created, now you will have to wait for somebody to enter your game.</h3><br>
					<img src="https://i.imgur.com/A7S7B5E.png"><br><br>
					<h3 style="display:inline">If you want to enter in a Rock, Paper, Scissors game, just press the Enter game button and then select your pick!</h3><br>
					<img src="https://i.imgur.com/1LYRO6p.png"><br>
					<img src="https://i.imgur.com/N0rGLnA.png"><br>
				</center>
			</div>
			<div id="miness" style="display:none">
				<center>
					<h3 style="display:inline">1. Select your bet amount and create a game using the "Start new game" button.</h3><br>
					<p style="display:inline">(For example I will bet 3 SBD)</p><br>
					<img src="https://i.imgur.com/2a0Km8M.png"><br><br>
					<h3 style="display:inline">Two new things have appeared, (1.) a cashout button, that you can press anytime to cashout your reward and (2.) a table, where the game is played</h3><br>
					<img src="https://i.imgur.com/UMMoq9d.png"><br><br>
					<h3 style="display:inline">The scope of the game is to hit as many blocks, without hitting a block with a mine under, everytime you hit a block without a mine your reward is increased by 13%. I have pressed two blocks (the green ones) and there were no bombs, so my reward increased. You can see your current reward in a message box under the navbar.</h3><br>
					<img src="https://i.imgur.com/TpvJn4C.png"><br><br>
					<h3 style="display:inline">If you hit a bomb (the red blocks), you lose and you can not cashout anymore, your reward is lost!</h3><br>
					<img src="https://i.imgur.com/Gze7s4n.png">
				</center>
			</div>
			<div id="rule" style="display:none">
					<h3 style="display:inline">Here you can see more items:<br> 1. The roulette table, where the rolling animation is played<br> 2. The last 5 rolls<br> 3. A countdown that counts down until the next roll (you have 60 seconds to bet and then there are 10 seconds in which you cannot bet and the roll happens)<br> 4. The bet buttons, if you bet on red or black and the roll is the same color as your bet you will win x2 your bet, if you bet on green and the roll is 0 or 00 you will win x14 your bet<br> 5. Your current balance.</h3><br>
					<center><img src="https://i.imgur.com/lLzM0M3.png"></center>
			</div>
		<?php include('src/footer.php'); ?>
		<script>
			function show(which) {
				$("#con").css("display", "none");
				$("#rpss").css("display", "none");
				$("#miness").css("display", "none");
				$("#rule").css("display", "none");
				if(which == 1) {
					$("#con").css("display", "block");
				} else if(which == 2) {
					$("#rpss").css("display", "block");
				} else if(which == 3) {
					$("#miness").css("display", "block");
				} else if(which == 4) {
					$("#rule").css("display", "block");
				}
			}
		</script>
	</body>
</html>