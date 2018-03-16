<?php 
require_once('src/utils.php');
?>

<ul class="nav">
	<li class="nav-logo"><a class="nav-link" href="http://steemcasino.com">SteemCasino</a></li>
<?php
	if(IsLoggedOnUser())
	{
		echo 
			'<li class="nav-item">
				<a class="nav-link" href="#" id=\'logout\' onclick="Logout();return false;">Logout</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="profile.php">My profile</a>
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
				<script>SetProfileInfo();</script>