<?php
function IsLoggedOnUser()
	{
		$verifiedUser = false;
		$authenticatedUser = false;

		if(isset($_COOKIE["username"]) && isset($_COOKIE["access_token"]))
		{
			$username = $_COOKIE["username"];
			$access_token = $_COOKIE["access_token"];

			$json_url = "https://steemconnect.com/api/me?access_token=".$access_token;
			$content = @file_get_contents($json_url);
			if($content !== FALSE)
			{
				$data = json_decode($content, TRUE);
				if($username = $data['user'])
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		else
		{
			return false;
		}
	}
?>