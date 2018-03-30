var game = 0;
var timer, reward, hash;

$("#table").hide();

function newGame(game, bet) {
	bet = $("#bet").val();
	$.getJSON( "../src/mines.php?action=newGame&game=" + game+"&bet=" + bet, function( data ) {
		console.log(data);
		if(data['status'] == 'error')
			errorGame(data['error'], data['message']);
		else if(data['status'] == 'success') {
			$("#cashout").text("Cash out");
			$("#newgame").text("");
			game = data['game'];
			hash = data['hash'];
			reward = data['reward'];
			
			$("#messages-box").css('background-color', 'green');
			$("#messages").text("A new game has started.");
			$("#hash").text("Hash: " + hash);
			$("#cashout").attr("onClick", "cashOut(" + game + ");");
			$("#newgame").attr("onClick", "newGame(" + game + ", bet);");
			$("#closeMessage").text("X");
			
			$("#bet").hide();
			$("#betn").hide();
			$("#table").show();
	
			clearInterval(timer);
			timer = setInterval(function() { closeMessage(); }, 1000 * 10);
		}
	});
}

function cashOut(game) {
	$.getJSON( "../src/mines.php?action=cashOut&game=" + game, function( data ) {
		console.log(data);
		if(data['status'] == 'error')
			errorGame(data['error'], data['message']);
		else if(data['status'] == 'success') {
			$("#cashout").text("");
			game = 0;
			$("#newgame").text("Start new game");
			
			$("#cashout").attr("onClick", "cashOut(0);");
			$("#newgame").attr("onClick", "newGame(0, bet);");
			
			$("#table").hide();
			
			$("#bet").show();
			$("#betn").show();
			
		}
	});
}

function hitBlock(game, block) {
	console.log(block);
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