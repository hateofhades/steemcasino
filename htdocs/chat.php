<?php 
require_once('src/utils.php');
?>
<html>
	<head>
		<style>
			.container {
				border: 2px solid #dedede;
				background-color: #f1f1f1;
				border-radius: 5px;
				padding: 10px;
				margin: 10px 0;
			}
			
			.time-left {
				float: left;
				color: #999;
			}
			
			.container img {
				float: left;
				max-width: 60px;
				width: 100%;
				max-height: 60px;
				margin-right: 20px;
				border-radius: 50%;
			}
		</style>
		<title>SteemCasino</title>

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
		<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.0/socket.io.js"></script>
		<script src="js/chat.js"></script>
		<script>
			SetProfileInfo();
			$(document).ready(function() {
			connect();
			hideChat();
			});
		</script>
	</head>
	<body style="background-color:white">
		<a id="close" href="" style="color:black;text-decoration:none" onClick="hideChat()">Close chat</a>
		<div class="chat" style="overflow-y:scroll;height:85%">
		</div>
		<div class="chat-input" style="height:10%;width:100%">
			<center><input style="width:80%;height:100%;overflow-wrap:break-word" id="btn-input" type="text" class="chat_input" placeholder="Write your message here..." />
            <span class="input-group-btn">
            <button class="btn btn-primary btn-sm" id="btn-chat" onClick="sendMessage()">Send</button></center>
		</div>
		<div class="openChat" style="background-color:yellow;position:fixed;bottom:5px;width:100%" onClick="showChat()"><center>Open chat</center></div>
	</body>
</html>