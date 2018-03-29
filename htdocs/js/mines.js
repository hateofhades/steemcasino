var game = 0;
var timer;

function newGame(game, bet) {
	bet = $("#bet").val();
	$.getJSON( "../src/mines.php?action=newGame&game=" + game+"&bet=" + bet, function( data ) {
		console.log(data);
		if(data['status'] == 'error')
			errorGame(data['error'], data['message']);
		else if(data['status'] == 'success') {
			$("#cashout").text("Cash out");
			$("#newgame").text("");
			console.log(data['game']);
		}
	});
}

function cashOut(game) {
	$("#cashout").text("");
	$("#newgame").text("Start new game");
}

function errorGame(errorCode, errorMessage) {
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