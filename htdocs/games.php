<html>
	<head>
		<link rel="stylesheet" href="dist/owl.carousel.min.css">
		<?php include('src/head.php'); ?>
		<script src="dist/owl.carousel.min.js"></script>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div class="default-body">
			<center><h1>SteemCasino's Games</h1></center>
			<center><h3>Choose a game</h3></center>
			<center>
				<div class="owl-carousel">
					<div>
						<a href="coinflip.php"><img height="20%" src="img/coinflip.png"></a>
					</div>
					<div>
						<a href="rps.php"><img height="20%" src="img/rockpaperscissors.png"></a>
					</div>
					<div>
						<a href="mines.php"><img height="20%" src="img/mines.png"></a>
					</div>
					<div>
						<a href="roulette.php"><img height="20%" src="img/roulette.png"></a>
					</div>
					<div>
						<a href="jackpot.php"><img height="20%" src="img/jackpot.png"></a>
					</div>
				</div><br>
				<a href="howtoplay.php">How to play?</a>
			</center>
		</div>
		
		<script>
			$(document).ready(function() {
 
				var owl = $('.owl-carousel');
				owl.owlCarousel({
					loop:false,
					margin:10,
					items: 8,
					touchDrag: true,
					mouseDrag: true,
				}); 
			});
		</script>
		<?php include('src/footer.php'); ?>
	</body>
</html>