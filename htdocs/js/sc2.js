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

					$("#accountName").append(result.account.name);
					if(profileImage)
					{
						$("#profileImage").attr("src", profileImage);
					}
				}
			}
			else
			{
				var loginUrl = sc2.getLoginURL();
				$("#login").attr("href", loginUrl);
			}
		});
	}
	else
	{
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

		location.reload();
	});
}