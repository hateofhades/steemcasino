sc2.init(
{
	app: 'steemcasino.app',
	callbackURL: sc2CallbackURL,
	scope: ['login'],
});

function SetProfileInfo()
{
	if (Cookies.get("access_token") != null)
	{
		sc2.setAccessToken(Cookies("access_token"));
		sc2.me(function (err, result)
		{
			if (!err)
			{
				if(result.account.json_metadata)
				{
					var profileImage = JSON.parse(result.account.json_metadata)['profile']['profile_image'];

					var accountName = result.account.name;
					if(profileImage)
					{
						$("#profileImage").attr("src", profileImage);
					}
				}
			}
			else
			{
				var url=location.href;
				var urlFilename = url.substring(url.lastIndexOf('/')+1);
		
				if(urlFilename != "index.php" && urlFilename != "credits.php" && urlFilename != "updates.php" && urlFilename != "about.php" && urlFilename != "faq.php")
				{
					window.location.href = "http://localhost/index.php";
				}
				
				var loginUrl = sc2.getLoginURL();
				$("#login").attr("href", loginUrl);
			}
		});
	}
	else
	{
		var url=location.href;
		var urlFilename = url.substring(url.lastIndexOf('/')+1);
		
		if(urlFilename != "index.php" && urlFilename != "credits.php" && urlFilename != "updates.php" && urlFilename != "about.php" && urlFilename != "faq.php")
		{
			window.location.href = "http://localhost/index.php";
		}
		
		var loginUrl = sc2.getLoginURL();
		$("#login").attr("href", loginUrl);
	}
}

function Logout()
{
	sc2.revokeToken(function (err, result)
	{
		Cookies.remove("access_token", { path: '/' });
		Cookies.remove("username", { path: '/' });
		Cookies.remove("expires_in", { path: '/' });
		Cookies.remove("privacy", { path: '/'});

		location.reload();
	});
}
			
function log10(str) {
    const leadingDigits = parseInt("" + str.substring(0, 4));
    const log = Math.log(leadingDigits) / Math.log(10)
    const n = str.length - 1;
    return n + (log - parseInt(log));
}