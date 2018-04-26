var HOST = "localhost:3000";
var socket = null;
var state;

var rolls;

var owl;

var timer;

var timeleft = 0;
var timeleftCounter;

function connect() {
	if(!socket) {
		SOCKET = io(HOST);
		getBalance();
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

function secondsToMS(s) {
    var m = Math.floor(s/60);
    s -= m*60;
    return (m < 10 ? '0'+m : m)+":"+(s < 10 ? '0'+s : s);
}

function getMessage(msg) {
	console.log(msg);
	
	if(msg['messageType'] == 11) {
		$("#circle").show();
		$("#circle").circleProgress('value', 0);
		$('.owl-carousel').trigger('destroy.owl.carousel');
		$(".owl-carousel").hide();
		$(".roulette-sign").hide();
		$(".ow-carousel").html("");
		$("#gameid").text("Game #" + msg['gameid']);
		timeleft = msg['timeleft'];
		clearInterval(timeleftCounter);
		$("#jackpottime").text(secondsToMS(timeleft));
		timeleftCounter = setInterval(timeleftMinus, 1000);
	}
	else if(msg['messageType'] == 5) {
		$('.owl-carousel').trigger('destroy.owl.carousel');
		$("#circle").circleProgress('value', msg['totalBet'] / 50);
		$(".owl-carousel").hide();
		$(".roulette-sign").hide();
		$(".ow-carousel").html("");
		var totalBetOn = "";
		$.each(msg['playerBet'], function(i, value) {
			totalBetOn = '<div><img height="24px" width="24px" style="vertical-align:middle" src="https://steemitimages.com/u/'+ value[0] +'/avatar"> - ' + value[0] + ' - ' + ((value[1] / msg['totalBet']) * 100).toFixed(2) + '% - ' + value[1] + ' SBD</div><div style="width:100%;height:1px;margin-top:2px;margin-bot:2px"></div>' + totalBetOn;
		});
		$("#contentJackpot").html(totalBetOn);
	}
	else if(msg['messageType'] == 6) {
		$(".owl-carousel").hide();
		$(".roulette-sign").hide();
		$(".owl-carousel").html("");
		$("#circle").show();
		$("#circle").circleProgress('value', 0);
		clearInterval(timeleftCounter);
		timeleft = msg['timeleft'];
		$("#gameid").text("Game #" + msg['gameid']);
		$("#jackpottime").text(secondsToMS(timeleft));
		timeleftCounter = setInterval(timeleftMinus, 1000);
	} else if(msg['messageType'] == 7) {
		$("#circle").hide();
		$(".owl-carousel").show();
		$(".roulette-sign").show();
		var winner = msg['winner'];
		var playerAndTickets = msg['playerAndTickets'];
		var winningTicket = msg['winningTicket'];
		var animation = "";
		var addedWinner = 0;
		var totalPlaces = 0;
		var ticketPlace = 0;
		for(var i=0; i<69; i++) {
			var ticket = Math.floor(Math.random() * 500001);
			var totalTickets = 0;
			var nomoar = 0;
			for(var y=0; y<playerAndTickets.length; y++) {
				totalTickets += playerAndTickets[y][1];
				if(totalTickets >= ticket && nomoar == 0) {
					animation = "<div style=\"border:1px solid black\" class=\"roulette\" data-hash=" + ticket +"><img src=\"https://steemitimages.com/u/" + playerAndTickets[y][0] + "/avatar\"></div>" + animation;
					nomoar = 1;
					if(addedWinner == 0)
						ticketPlace++;
				}
				if(totalTickets >= winningTicket && addedWinner == 0) {
					addedWinner = 1;
					animation = '<div style="border:1px solid black" class="roulette" data-hash="winner"><img src="https://steemitimages.com/u/' + playerAndTickets[y][0] + '/avatar"></div>' + animation;
				}
				if(nomoar == 1 && addedWinner == 1)
					break;
			}

		}
		$(".owl-carousel").html(animation);
		owl = $('.owl-carousel');
		owl.owlCarousel({
			loop:true,
			margin:0,
			nav:false,
			URLhashListener:true,
			startPosition: 'URLHash',
			touchDrag: false,
			mouseDrag: false,
			autoWidth:true,
			center: true,
			responsive:{
				0:{
					items:3
				},
				600:{
					items:5
				},
				1000:{
					items:7
				}
			}
		});
		
		
		
		var totalRolls = 69 * 2 + ticketPlace;
		
		console.log(totalRolls);
		
		window.location.href = "#rolling";
		
		rolls = 0;
		
		roll(totalRolls);
	}
}

function roll(howMuch) {
	rolls++;
	if(rolls < howMuch) {
		owl.trigger('next.owl.carousel', [100]);
	setTimeout(function() {roll(howMuch);}, 80);
	} else {
		window.location.href = "#winner";
		setTimeout(function() {resetPage();}, 5000);
	}
}

function betJackpot() {
	$("#messages-box").css('background-color', 'yellow');
	$("#messages").text("Working...");
	$("#closeMessage").text("X");
			
	clearInterval(timer);
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
		
	bet = $("#bet").val();
		
	$.getJSON( "../src/jackpot.php?bet=" + bet, function( data ) {
		if(data['status'] == 'error')
			errorGame(data['error'], data['message']);
		else if(data['status'] == 'success') {
			$("#messages-box").css('background-color', 'green');
			$("#messages").text(data['message']);
			$("#closeMessage").text("X");
			
			$("#balance").text("Balance: " + data['balance'] + " SBD");
			
			clearInterval(timer);
			timer = setInterval(function() { closeMessage(); }, 1000 * 10);
		}
	});
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

function resetPage() {
	window.location.href = "jackpot.php";
}

function timeleftMinus() {
	timeleft--;
	$("#jackpottime").text(secondsToMS(timeleft));
	if(timeleft == 0)
		clearInterval(timeleftCounter);
}