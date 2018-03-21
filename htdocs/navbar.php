<?php 
require_once('src/utils.php');
?>

<ul class="nav">
	<li class="nav-logo"><a class="nav-link-img" href="http://steemcasino.org"><img src="img/logo.png"></a></li>
	<li class="nav-logo"><a class="nav-link" href="http://steemcasino.org">SteemCasino</a></li>
<?php
	if(IsLoggedOnUser())
	{
		echo 
			'<li class="nav-item">
				<a class="nav-link" href="#" id=\'logout\' onclick="Logout();return false;">Logout</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="profile.php">My profile</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="games.php">Games</a>
			</li>';
	}
	else
	{
		echo 
			'<li class="nav-item">
				<a class="nav-link" href="" id=\'login\'>Login</a>
			</li>';
	}
?>	
</ul>
				<script>SetProfileInfo();
				console.log(IsValidToken());
				</script>