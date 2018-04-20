var timer;
var HOST = "localhost:3000";
var socket = null;
var progress;
var state;

function connect() {
	if(!socket) {
		getBalance();
		SOCKET = io(HOST);
			SOCKET.on('connect', function(msg) {
				console.log("You have been connected to the socket.");
				SOCKET.emit('username', Cookies.get("username"));
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
	
	if(msg['messageType'] == 1) {	
		var currentUrl = window.location.href;
		notPhp = currentUrl.indexOf("#");
		
		if(notPhp != -1) {
			currentUrl = currentUrl.replace(currentUrl.substring(currentUrl.indexOf("#")+1), "");
			if(msg['lastRolls'][0] != 37)	
				currentUrl = currentUrl + msg['lastRolls'][0];
			else
				currentUrl = currentUrl + "zz";
			window.location.href = currentUrl;
		} else {
			if(msg['lastRolls'][0] != 37)
				window.location.href = "#" + msg['lastRolls'][0];
			else
				window.location.href = "#zz";
		}
		
		displayLastRolls(msg['lastRolls']);
		state = msg['state'];
		clearTimeout(progress);
		displayProgressBar(msg['timestamp'] - Math.floor($.now()/1000));
		
		setButtons();
	}
	else if(msg['messageType'] == 2) {
		playAnimation(msg['lastRolls'][0]);
		
		setTimeout(function() {displayLastRolls(msg['lastRolls']);}, 10000);
		
		clearTimeout(progress);
		state = 1;
		
		displayProgressBar(msg['timestamp']);
		
		setButtons();
	} else if(msg['messageType'] == 3) {
		clearTimeout(progress);
		state = 0;
		
		displayProgressBar(msg['timestamp']);
		
		setButtons();
		
		getBalance();
	} else if(msg['messageType'] == 4) {
		$("#totalRed").text("Total: " + msg['redBet'] + " SBD");
		$("#totalBlack").text("Total: " + msg['blackBet'] + " SBD");
		$("#totalGreen").text("Total: " + msg['greenBet'] + " SBD");
		
		var redPlayers = "", blackPlayers = "", greenPlayers = "";
		$.each(msg['redPlayers'], function(i, value) {
			redPlayers = '<div><img width="10%" style="vertical-align:middle" src="https://steemitimages.com/u/'+ value[0] +'/avatar"> - ' + value[0] + ' - ' + value[1] + ' SBD</div><div style="width:100%;height:1px;margin-top:2px;margin-bot:2px"></div>' + redPlayers;
		});
		$.each(msg['blackPlayers'], function(i, value) {
			blackPlayers = '<div><img width="10%" style="vertical-align:middle" src="https://steemitimages.com/u/'+ value[0] +'/avatar"> - ' + value[0] + ' - ' + value[1] + ' SBD</div><div style="width:100%;height:1px;margin-top:2px;margin-bot:2px"></div>' + blackPlayers;
		});
		$.each(msg['greenPlayers'], function(i, value) {
			greenPlayers = '<div><img width="10%" style="vertical-align:middle" src="https://steemitimages.com/u/'+ value[0] +'/avatar"> - ' + value[0] + ' - ' + value[1] + ' SBD</div><div style="width:100%;height:1px;margin-top:2px;margin-bot:2px"></div>' + greenPlayers;
		});
		
		$("#contentRed").html(redPlayers);
		$("#contentBlack").html(blackPlayers);
		$("#contentGreen").html(greenPlayers);
	}	
}

function setButtons() {
	if(state) {
		$("#btn1").attr("disabled", "disabled");
		$("#btn2").attr("disabled", "disabled");
		$("#btn3").attr("disabled", "disabled");
	} else {
		$("#btn1").removeAttr("disabled");
		$("#btn2").removeAttr("disabled");
		$("#btn3").removeAttr("disabled");
	}
}

function displayProgressBar(timestamp) {
	if(state == 0 && timestamp >= 0) {
		$("#progress").attr("max", 60);
		$("#progress").attr("value", timestamp);
		$("#progressText").text(timestamp + " seconds");
		timestamp = timestamp - 1;
		progress = setTimeout(function() {displayProgressBar(timestamp)}, 1000);
	} else if (state == 1 && timestamp >= 0) {
		$("#progress").attr("max", 10);
		$("#progress").attr("value", timestamp);
		$("#progressText").text(timestamp + " seconds");
		timestamp = timestamp - 1;
		progress = setTimeout(function() {displayProgressBar(timestamp)}, 1000);
	}
}

function displayLastRolls(lastRolls) {
	for(var i = 0; i<5; i++) {
		var color = getColor(lastRolls[i]);
		var block = i + 1;
		if(color == 1) {
			$("#last" + block).css("background-color", "red");
			$("#last" + block).text(lastRolls[i]);
		}
		else if(color == 2) {
			$("#last" + block).css("background-color", "black");
			$("#last" + block).text(lastRolls[i]);
		}
		else if(color == 3) {
			$("#last" + block).css("background-color", "green");
			if(lastRolls[i] == 37)
				$("#last" + block).text("00");
			else
				$("#last" + block).text("0");
		}
	}
}

function getColor(Rolled) {
	if(Rolled == 1 || Rolled == 3 || Rolled == 5 || Rolled == 7 || Rolled == 9 || Rolled == 36 || Rolled == 34 || Rolled == 32 || Rolled == 30 || Rolled == 14 || Rolled == 16 || Rolled == 18 || Rolled == 12 || Rolled == 27 || Rolled == 23 || Rolled == 21 || Rolled == 19 || Rolled == 25)
		return 1;
	else if(Rolled == 13 || Rolled == 24 || Rolled == 15 || Rolled == 22 || Rolled == 17 || Rolled == 20 || Rolled == 11 || Rolled == 26 || Rolled == 28 || Rolled == 2 || Rolled == 35 || Rolled == 4 || Rolled == 33 || Rolled == 6 || Rolled == 31 || Rolled == 8 || Rolled == 29 || Rolled == 10)
		return 2;
	else if(Rolled == 0 || Rolled == 37)
		return 3;
}

function getUnder() {
	var currentUrl = window.location.href;
	var under = 0;
	notPhp = currentUrl.indexOf("#");
	
	if(notPhp != -1) {
		currentUrl = currentUrl.substring(currentUrl.indexOf("#")+1);
		
		if(currentUrl > 36 && currentUrl != 0 || currentUrl == "" || currentUrl == "00")
			under = 0;
		else if(currentUrl == "zz")
			under = 37;
		else
			under = currentUrl;
	} else 
		under = 0;
	
	return under;
}

function calculateZero() {
	var under = getUnder();
	var returned;
	
	if(under == 0)
		returned = 1;
	else if(under == 1)
		returned = 38;
	else if(under == 13)
		returned = 37;
	else if(under == 36)
		returned = 36;
	else if(under == 24)
		returned = 35;
	else if(under == 3)
		returned = 34;
	else if(under == 15)
		returned = 33;
	else if(under == 34)
		returned = 32;
	else if(under == 22)
		returned = 31;
	else if(under == 5)
		returned = 30;
	else if(under == 17)
		returned = 29;
	else if(under == 32)
		returned = 28;
	else if(under == 20)
		returned = 27;
	else if(under == 7)
		returned = 26;
	else if(under == 11)
		returned = 25;
	else if(under == 30)
		returned = 24;
	else if(under == 26)
		returned = 23;
	else if(under == 9)
		returned = 22;
	else if(under == 28)
		returned = 21;
	else if(under == 37)
		returned = 20;
	else if(under == 2)
		returned = 19;
	else if(under == 14)
		returned = 18;
	else if(under == 35)
		returned = 17;
	else if(under == 23)
		returned = 16;
	else if(under == 4)
		returned = 15;
	else if(under == 16)
		returned = 14;
	else if(under == 33)
		returned = 13;
	else if(under == 21)
		returned = 12;
	else if(under == 6)
		returned = 11;
	else if(under == 18)
		returned = 10;
	else if(under == 31)
		returned = 9;
	else if(under == 19)
		returned = 8;
	else if(under == 8)
		returned = 7;
	else if(under == 12)
		returned = 6;
	else if(under == 29)
		returned = 5;
	else if(under == 25)
		returned = 4;
	else if(under == 10)
		returned = 3;
	else if(under == 27)
		returned = 2;
	
	return returned;
}

function playAnimation(onWath) {
	var moves = calculateZero();
	moves = moves + 38;
	
	var howMuch;
	
	if(onWath == 0)
		howMuch = 0;
	else if(onWath == 1)
		howMuch = 1;
	else if(onWath == 13)
		howMuch = 2;
	else if(onWath == 36)
		howMuch = 3;
	else if(onWath == 24)
		howMuch = 4;
	else if(onWath == 3)
		howMuch = 5;
	else if(onWath == 15)
		howMuch = 6;
	else if(onWath == 34)
		howMuch = 7;
	else if(onWath == 22)
		howMuch = 8;
	else if(onWath == 5)
		howMuch = 9;
	else if(onWath == 17)
		howMuch = 10;
	else if(onWath == 32)
		howMuch = 11;
	else if(onWath == 20)
		howMuch = 12;
	else if(onWath == 7)
		howMuch = 13;
	else if(onWath == 11)
		howMuch = 14;
	else if(onWath == 30)
		howMuch = 15;
	else if(onWath == 26)
		howMuch = 16;
	else if(onWath == 9)
		howMuch = 17;
	else if(onWath == 28)
		howMuch = 18;
	else if(onWath == 37)
		howMuch = 19;
	else if(onWath == 2)
		howMuch = 20;
	else if(onWath == 14)
		howMuch = 21;
	else if(onWath == 35)
		howMuch = 22;
	else if(onWath == 23)
		howMuch = 23;
	else if(onWath == 4)
		howMuch = 24;
	else if(onWath == 16)
		howMuch = 25;
	else if(onWath == 33)
		howMuch = 26;
	else if(onWath == 21)
		howMuch = 27;
	else if(onWath == 6)
		howMuch = 28;
	else if(onWath == 18)
		howMuch = 29;
	else if(onWath == 31)
		howMuch = 30;
	else if(onWath == 19)
		howMuch = 31;
	else if(onWath == 8)
		howMuch = 32;
	else if(onWath == 12)
		howMuch = 33;
	else if(onWath == 29)
		howMuch = 34;
	else if(onWath == 25)
		howMuch = 35;
	else if(onWath == 10)
		howMuch = 36;
	else if(onWath == 27)
		howMuch = 37;
	
	moves = moves + howMuch;
	
	i = 0;
	roll(moves);
}

function betRoulette(betOn) {
	if(betOn == 1 || betOn == 2 || betOn == 3) {
		$("#messages-box").css('background-color', 'yellow');
		$("#messages").text("Working...");
		$("#closeMessage").text("X");
			
		clearInterval(timer);
		timer = setInterval(function() { closeMessage(); }, 1000 * 10);
		
		bet = $("#bet").val();
		
		$.getJSON( "../src/roulette.php?betOn=" + betOn + "&bet=" + bet, function( data ) {
			if(data['status'] == 'error')
				errorGame(data['error'], data['message']);
			else if(data['status'] == 'success') {
				$("#messages-box").css('background-color', 'green');
				$("#messages").text(data['message']);
				$("#closeMessage").text("X");
				
				$("#balance").text("Your balance: " + data['balance'] + " SBD");
				
				clearInterval(timer);
				timer = setInterval(function() { closeMessage(); }, 1000 * 10);
			}
		});
	}
}

function getBalance() {
	$.getJSON( "../src/getbalance.php", function( data ) {
		if(data['status'] == 'success') {
			$("#balance").text("Balance: " + data['balance'] + " SBD");
		}
	});
}

function closeMessage() {
	clearInterval(timer);
	$("#messages").text("");
	$("#closeMessage").text("");
}

function errorGame(errorCode, errorMessage) {
	$("#messages-box").css('background-color', 'red');
	$("#messages").text("Error " + errorCode + ": " + errorMessage);
	$("#closeMessage").text("X");
	
	clearInterval(timer);
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
}