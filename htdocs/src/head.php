<title>SteemCasino</title>

		<link rel="stylesheet" type="text/css" href="src/design.css">

		<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css" />
		<script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.js"></script>
		<script>
		window.addEventListener("load", function(){
		window.cookieconsent.initialise({
		  "palette": {
			"popup": {
			  "background": "#237afc"
			},
			"button": {
			  "background": "#fff",
			  "text": "#237afc"
			}
		  }
		})});
		</script>
		
		<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@2.2.0/src/js.cookie.min.js"></script>
		<script type="text/javascript" src="js/config.js"></script>
		<script type="text/javascript" src="js/sc2.min.js"></script>
		<script type="text/javascript" src="js/sc2.js?V=2"></script>

		
		<script>
			if (Cookies.get("privacy") != null) {
				var url=location.href;
				var urlFilename = url.substring(url.lastIndexOf('/')+1);
				if(urlFilename != "privacypolicy.php") {
					urlFilename = urlFilename.substring(0,12);
					
					if(Cookies.get("privacy") == 0)
						if(urlFilename != "loggedin.php")
							window.location = "privacypolicy.php";
				}
			}
		</script>