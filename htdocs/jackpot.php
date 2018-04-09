<html>
	<head>
		<link rel="stylesheet" href="dist/owl.carousel.min.css">
		<?php include('src/head.php'); ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.0/socket.io.js"></script>
		<script src="/dist/circle-progress.js"></script>
		<script src="/js/jackpot.js"></script>
		<script src="dist/owl.carousel.min.js"></script>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<center><h1 style="display:inline">Jackpot </h1><b><a href="games.php" style="display:inline;text-decoration:none;color:black;">(Go back) </a></b></center><br>
		<center>
			<div class="roulette-box">
				<div class="roulette-sign" style="display:none""></div>
				<div class="owl-carousel" style="width:80%">
				</div>
			</div>
			<div id="circle" style="position:relative">
				<strong id="gameid" style="position:absolute;top:60px;left:0;width:100%"></strong>
				<strong id="totals" style="position:absolute;top:90px;left:0;width:100%"></strong>
				<strong id="jackpottime" style="position:absolute;top:120px;left:0;width:100%">00:00</strong>
			</div>
			<div 
		</center>
		<?php include('src/footer.php'); ?>
		<script>
		  $('#circle').circleProgress({
			value: 0,
			size: 200,
			fill: {gradient: [['#0681c4', .5], ['#4ac5f8', .5]], gradientAngle: Math.PI / 4}
			}).on('circle-animation-progress', function(event, progress, stepValue) {
				$("#totals").text((stepValue * 50).toFixed(3) + "/50 SBD");
		  });
		  
		  $(document).ready(function() {
			connect();
		  });
		</script>
	</body>
</html>