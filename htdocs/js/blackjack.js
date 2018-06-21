var game=0, timer, insuranced;

function deal() {
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
	$("#messages-box").css('background-color', 'yellow');
	$("#messages").text("Working...");
	$("#closeMessage").text("X");
	
	var bet = $("#bet").val();
	
	if(game == 0)
		$.getJSON( "../src/blackjack.php?action=new&bet=" + bet, function( data ) {
			console.log(data);
			
			if(data['status'] == 'error')
				errorGame(data['error'], data['message']);
			else {
				insuranced = 0;
				$("#hash").text("Hash: " + data['hash']);
				$("#gameStatus").text("Let's play!");
				$("#dealerHandString").text("Dealer:");
				$("#playerHandString").text("Player:");
				$("#secret").text("");
				closeMessage();
				timer = setInterval(function() { closeMessage(); }, 1000 * 10);
				$("#messages-box").css('background-color', 'green');
				$("#messages").text("Game started.");
				$("#closeMessage").text("X");
				
				game = data['game'];
				$("#balance").text("Balance: " + data['balance'] + " SBD");
				
				$("#playerHandString").text("Player: " + data['points']);
				
				createGame(data['playerDraw'], data['houseDraw'], data['blackjack'], data['insurance'], data['secret']);
			}
		});
	else {
		closeMessage();
		timer = setInterval(function() { closeMessage(); }, 1000 * 10);
		$("#messages-box").css('background-color', 'red');
		$("#messages").text("A game is already started.");
		$("#closeMessage").text("X");
	}
}

function hit() {
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
	$("#messages-box").css('background-color', 'yellow');
	$("#messages").text("Working...");
	$("#closeMessage").text("X");
	
	if(game)
		$.getJSON( "../src/blackjack.php?action=hit&game=" + game, function( data ) {
			console.log(data);
			
			if(data['status'] == 'error')
				errorGame(data['error'], data['message']);
			else {
				$("#surrender").css("background-color", "#C0DBD1");
				$("#doubled").css("background-color", "#C0DBD1");
				
				closeMessage();
				
				$("#playerHandString").text("Player: " + data['points']);
				
				updateGame(data['playerHand'], data['house'], data['message'], data['win'], data['housePoints'], data['secret']);
			}
		});
}

function stand() {
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
	$("#messages-box").css('background-color', 'yellow');
	$("#messages").text("Working...");
	$("#closeMessage").text("X");
	
	if(game)
		$.getJSON( "../src/blackjack.php?action=stand&game=" + game, function( data ) {
			console.log(data);
			
			if(data['status'] == 'error')
				errorGame(data['error'], data['message']);
			else {
				$("#surrender").css("background-color", "#C0DBD1");
				
				closeMessage();
				
				standGame(data['house'], data['win'], data['housePoints'], data['secret']);
			}
		});
}

function doubled() {
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
	$("#messages-box").css('background-color', 'yellow');
	$("#messages").text("Working...");
	$("#closeMessage").text("X");
	
	if(game)
		$.getJSON( "../src/blackjack.php?action=doubled&game=" + game, function( data ) {
			console.log(data);
			
			if(data['status'] == 'error')
				errorGame(data['error'], data['message']);
			else {
				$("#surrender").css("background-color", "#C0DBD1");
				$("#balance").text("Balance: " + data['balance'] + " SBD");
				$("#dealerHandString").text("Dealer: " + data['housePoints']);
				$("#secret").text("Secret: " + data['secret']);
				$("#playerHandString").text("Player: " + data['points']);
				
				closeMessage();
				
				doubleGame(data['player'], data['house'], data['win'], data['statuss']);
			}
		});
}

function surrender() {
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
	$("#messages-box").css('background-color', 'yellow');
	$("#messages").text("Working...");
	$("#closeMessage").text("X");
	
	if(game)
		$.getJSON( "../src/blackjack.php?action=surrender&game=" + game, function( data ) {
			console.log(data);
			
			if(data['status'] == 'error')
				errorGame(data['error'], data['message']);
			else {	
				closeMessage();
				
				$("#balance").text("Balance: " + data['balance'] + " SBD");
				setButtons(1, 0, 0, 0, 0, 0, 0);
				game = 0;
				setTable("player", []);
				setTable("dealer", []);
				$("#playerHandString").text("Player:");
			}
		});
}

function insurance() {
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
	$("#messages-box").css('background-color', 'yellow');
	$("#messages").text("Working...");
	$("#closeMessage").text("X");
	
	if(game)
		$.getJSON( "../src/blackjack.php?action=insurance&game=" + game, function( data ) {
			console.log(data);
			
			if(data['status'] == 'error')
				errorGame(data['error'], data['message']);
			else {	
				insuranced = 1;
				closeMessage();
				
				$("#balance").text("Balance: " + data['balance'] + " SBD");
				$("#gameStatus").text("Insurance");
				
				setButtons(0, 1, 1, 0, 0, 0, 1);
			}
		});
}

function doubleGame(playerHand, houseHand, win, statuss) {
	setTable("player", playerHand);
	setTable("dealer", houseHand);
	setButtons(1, 0, 0, 0, 0, 0, 0);
	game = 0;
	
	if(win == 1) {
		$("#gameStatus").text("You won.");
	} else if(win == 2) {
		if(statuss == 2)
			$("#gameStatus").text("You lost. Dealer had blackjack.");
		else
			$("#gameStatus").text("You lost.");
	} else
		$("#gameStatus").text("Draw.");
}

function createGame(playerHand, houseHand, isBj, insurance, secret) {
	setTable("player", playerHand);
	setTable("dealer", houseHand);
	
	setButtons(0, 1, 1, insurance, 1, 0, 1);
	
	if(isBj == 1) {
		$("#gameStatus").text("You win.");
		$("#secret").text("Secret: " + secret);
		setButtons(1, 0, 0, 0, 0, 0, 0);
		game = 0;
	}
	else if(isBj == 2) {
		$("#gameStatus").text("Draw.");
		$("#secret").text("Secret: " + secret);
		setButtons(1, 0, 0, 0, 0, 0, 0);
		game = 0;
	} else if(isBj == 3) {
		$("#gameStatus").text("House had blackjack. You lost.");
		$("#secret").text("Secret: " + secret);
		setButtons(1, 0, 0, 0, 0, 0, 0);
		game = 0;
	}
}

function updateGame(playerHand, houseHand, type, win, points, secret) {
	if(type == 1) {
		setTable("player", playerHand);
		if(win) {
			$("#secret").text("Secret: " + secret);
			$("#gameStatus").text("You lost.");
			setButtons(1, 0, 0, 0, 0, 0, 0);
			game = 0;
			if(insuranced)
				getBalance();
		}
	}
	else {
		setTable("player", playerHand);
		setTable("dealer", houseHand);
		setButtons(1, 0, 0, 0, 0, 0, 0);
		game = 0;
		
		$("#dealerHandString").text("Dealer: " + points);
		$("#secret").text("Secret: " + secret);
		
		if(type == 2) {
			$("#gameStatus").text("You lose.");
			if(insuranced)
				getBalance();
		}
		else if(type == 3) {
			$("#gameStatus").text("You win.");
			getBalance();
		}
		else if(type == 4) {
			$("#gameStatus").text("Draw.");
			getBalance();
		}
		else if(type == 5) {
			$("#gameStatus").text("You lose. Dealer had blackjack.");
			if(insuranced)
				getBalance();
		}
	}
}

function standGame(houseHand, win, points, secret) {
	setButtons(1, 0, 0, 0, 0, 0, 0);
	setTable("dealer", houseHand);
	game = 0;
	
	$("#dealerHandString").text("Dealer: " + points);
	$("#secret").text("Secret: " + secret);
	
	if(win == 1) {
		$("#gameStatus").text("You win.");
		getBalance();
	} else if(win == 2)  {
		$("#gameStatus").text("You lose.");
		if(insuranced)
				getBalance();
	}
	else if(win == 3) {
		$("#gameStatus").text("Draw.");
		getBalance();
	}
}

function searchCard(card) {
	return cardURL = "img/blackjack/" + card[0] + card[1] + ".png";
}

function setButtons(deal = 0, hit = 1, stand = 1, insurance = 0, doubled = 1, split = 0, surrender = 1) {
	if(deal)
		$("#deal").css("background-color", "white");
	else
		$("#deal").css("background-color", "#C0DBD1");
	
	if(hit)
		$("#hit").css("background-color", "white");
	else
		$("#hit").css("background-color", "#C0DBD1");
	
	if(stand)
		$("#stand").css("background-color", "white");
	else
		$("#stand").css("background-color", "#C0DBD1");
	
	if(insurance)
		$("#insurance").css("background-color", "white");
	else
		$("#insurance").css("background-color", "#C0DBD1");
	
	if(doubled)
		$("#doubled").css("background-color", "white");
	else
		$("#doubled").css("background-color", "#C0DBD1");
	
	if(split)
		$("#split").css("background-color", "white");
	else
		$("#split").css("background-color", "#C0DBD1");
	
	if(surrender)
		$("#surrender").css("background-color", "white");
	else
		$("#surrender").css("background-color", "#C0DBD1");
}

function getBalance() {
	$.getJSON( "../src/getbalance.php", function( data ) {
		if(data['status'] == 'success') {
			$("#balance").text("Balance: " + data['balance'] + " SBD");
		}
	});
}

function setTable(who, hand) {
	var handString = "<center>";
	var i;
	
	for(i = 0; i < hand.length; i++) {
		var cardURL = searchCard(hand[i]);
		handString = handString + "<img src=\"" + cardURL + "\" style=\"display:inline\" height=\"100%\">";
	}
	
	if(i == 1)
		handString = handString + "<img src=\"img/blackjack/cardback.png\" style=\"display:inline\" height=\"100%\">";
	
	handString = handString + "</center>";
	
	$("#" + who + "Hand").html(handString);
}

function errorGame(errorCode, errorMessage) {
	$("#messages-box").css('background-color', 'red');
	$("#messages").text("Error " + errorCode + ": " + errorMessage);
	$("#closeMessage").text("X");
	
	clearInterval(timer);
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
}

function closeMessage() {
	clearInterval(timer);
	$("#messages").text("");
	$("#closeMessage").text("");
}