<?php
include_once('src/db.php');

?>

<html>
	<head>
		<link rel="stylesheet" href="dist/owl.carousel.min.css">
		<?php include('src/head.php'); ?>
		<meta http-equiv="refresh" content="<?php echo $secrefresh?>;URL='<?php echo $page?><?php if($past == 1) echo "?past=1";?>'">
		<script src="dist/owl.carousel.min.js"></script>
		<script src="js/roulette.js"></script>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div class="roulette-body">
			<center><h1 style="display:inline">Roulette </h1><b><a href="games.php" style="display:inline;text-decoration:none;color:black;">(Go back)</a></b>
			<div class="owl-carousel">
			  <div data-hash="0" > 0 </div>
			  <div data-hash="1" > 1 </div>
			  <div data-hash="13" > 13 </div>
			  <div data-hash="36" > 36 </div>
			  <div data-hash="24" > 24 </div>
			  <div data-hash="3" > 3 </div>
			  <div data-hash="15" > 15 </div>
			  <div data-hash="34" > 34 </div>
			  <div data-hash="22" > 22 </div>
			  <div data-hash="5" > 5 </div>
			  <div data-hash="17" > 17 </div>
			  <div data-hash="32" > 32 </div>
			  <div data-hash="20" > 20 </div>
			  <div data-hash="7" > 7 </div>
			  <div data-hash="11" > 11 </div>
			  <div data-hash="30" > 30 </div>
			  <div data-hash="26" > 26 </div>
			  <div data-hash="9" > 9 </div>
			  <div data-hash="28" > 28 </div>
			  <div data-hash="00" > 00 </div>
			  <div data-hash="2" > 2 </div>
			  <div data-hash="14" > 14 </div>
			  <div data-hash="35" > 35 </div>
			  <div data-hash="23" > 23 </div>
			  <div data-hash="4" > 4 </div>
			  <div data-hash="16" > 16 </div>
			  <div data-hash="33" > 33 </div>
			  <div data-hash="21" > 21 </div>
			  <div data-hash="6" > 6 </div>
			  <div data-hash="18" > 18 </div>
			  <div data-hash="31" > 31 </div>
			  <div data-hash="19" > 19 </div>
			  <div data-hash="8" > 8 </div>
			  <div data-hash="12" > 12 </div>
			  <div data-hash="29" > 29 </div>
			  <div data-hash="25" > 25 </div>
			  <div data-hash="10" > 10 </div>
			  <div data-hash="27" > 27 </div>
			</div>
		</div>
		
		<a href="#" onclick="createGame()">Create game</a>
		<script>
			$(document).ready(function() {
 
				var owl = $('.owl-carousel');
				owl.owlCarousel({
					loop:true,
					margin:10,
					nav:false,
					URLhashListener:true,
					startPosition: 'URLHash',
					center: true,
					responsive:{
						0:{
							items:3
						},
						600:{
							items:5
						},
						1000:{
							items:7
						}
					}
				});
				
				var i;
				
				function roll(howMuch) {
					i++;
					if(i < howMuch) {
						owl.trigger('next.owl.carousel', [200]);
						setTimeout(function() {roll();}, 150);
					}
				}
 
			});
		</script>
		<?php include('src/footer.php'); ?>
	</body>
</html>