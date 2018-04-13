<?php 
require_once('src/utils.php');
?>

<ul class="nav">
	<li class="nav-logo"><a class="nav-link-img" href="index.php"><img src="img/fullwhite-horizontal.png" height="40px"></a></li>
<?php
	if(IsLoggedOnUser())
	{
		echo 
			'<li class="nav-item">
				<a class="nav-link" href="#" id=\'logout\' onclick="Logout();return false;"><b>Logout</b></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="profile.php">My profile</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="games.php">Games</a>
			</li>
			<li class="nav-item">
				<a id="balance" class="nav-link"></a> 
			</li>';
	}
	else
	{
		echo 
			'
			<li class="nav-item">
				<a class="nav-link" href="" id=\'login\'><b>Login</b></a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="https://signup.steemit.com/"><b>Sign up</b></a>
			</li>';
	}
?>	
</ul>
				<script>
				SetProfileInfo();
				function getBalanced() {
					$.getJSON( "../src/getbalance.php", function( data ) {
					if(data['status'] == 'success') {
						$("#balance").text("Balance: " + data['balance'] + " SBD");
					}
				});
				}
				
				getBalanced();
				</script>
