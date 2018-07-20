var HOST = "localhost:3000";
var socket = null;
var message = 0;

var chatContent;

function connect() {
	if(!socket) {
		SOCKET = io(HOST);
			SOCKET.on('connect', function(msg) {
				console.log("You have been connected to the socket.");
				
				chatContent = '<div class="container"><img src="img/gray-vertical.png" alt="Avatar"><p style="color:green">You have been connected to the chat.</p></div>';
				
				$(".chat").html(chatContent);
				
				SOCKET.emit('usernameChat', Cookies.get("username") + " " + Cookies.get('access_token'));
			});
			SOCKET.on('connect_error', function(msg) {
				console.error(msg);
			});
			SOCKET.on('message', function(msg) {
				getMessage(msg);
			});
	} else {
		console.log('Connexion already exists.');
	}
}

function getMessage(msg) {
	console.log(msg);
	
	if(msg['messageType'] == "chat") {
		displayMessage(msg['user'], msg['content']);
	}
	else if(msg['messageType'] == "help" && msg['user'] == Cookies.get("username")) {
		chatContent = '<div class="container"><img src="img/gray-vertical.png" alt="Avatar"><p>Commands:\n/sendCoins (username) (amount)</p></div>' + $(".chat").html();
		$(".chat").html(chatContent);
	} else if(msg['messageType'] == "noBal" && msg['user'] == Cookies.get("username")) {
		chatContent = '<div class="container"><img src="img/gray-vertical.png" alt="Avatar"><p style="color:red">Not enough balance!</p></div>' + $(".chat").html();
		$(".chat").html(chatContent);
	} else if(msg['messageType'] == "noUsr" && msg['user'] == Cookies.get("username")) {
		chatContent = '<div class="container"><img src="img/gray-vertical.png" alt="Avatar"><p style="color:red">User does not exist.</p></div>' + $(".chat").html();
		$(".chat").html(chatContent);
	} else if(msg['messageType'] == "urself" && msg['user'] == Cookies.get("username")) {
		chatContent = '<div class="container"><img src="img/gray-vertical.png" alt="Avatar"><p style="color:red">You can\'t send coins to yourself!</p></div>' + $(".chat").html();
		$(".chat").html(chatContent);
	} else if(msg['messageType'] == "sendCoin" && msg['user'] == Cookies.get("username")) {
		chatContent = '<div class="container"><img src="img/gray-vertical.png" alt="Avatar"><p style="color:green">Successfully sent coins to: ' + msg['recive'] + '!</p></div>' + $(".chat").html();
		$(".chat").html(chatContent);
	} else if(msg['messageType'] == "sendCoin" && msg['recive'] == Cookies.get("username")) {
		chatContent = '<div class="container"><img src="img/gray-vertical.png" alt="Avatar"><p style="color:green">You have recived coins from: ' + msg['user'] + '!</p></div>' + $(".chat").html();
		$(".chat").html(chatContent);
	}
}

function displayMessage(user, content) {
	
	var dt = new Date();
	var time = dt.getHours() + ":" + dt.getMinutes();
	
	chatContent = '<div class="container"><img alt="Avatar" src="https://steemitimages.com/u/' + user +'/avatar"><p style="overflow-wrap:break-word" id="msg-' + message + '"></p><span class="time-right">' + user + ' - ' + time + '</span></div>' + $(".chat").html();
	$(".chat").html(chatContent);
	$("#msg-" + message).text(content);
	
	message = message + 1;
}

function sendMessage() {
	if($(".chat_input").val().length > 3 || $(".chat_input").val().length < 250) {
	SOCKET.emit('sendMessage', Cookies.get("username") + "|" + Cookies.get('access_token') + "|" + $(".chat_input").val());
	$(".chat_input").val("");
	}
}

function hideChat() {
	$("body").css("background-color", "");
	$(".chat").hide();
	$(".chat-input").hide();
	$(".openChat").show();
	$("#close").hide();
}

function showChat() {
	$("body").css("background-color", "white");
	$(".chat").show();
	$(".chat-input").show();
	$(".openChat").hide();
	$("#close").show();
}