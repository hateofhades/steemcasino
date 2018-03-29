var game = 0;

function newGame(user, token, game) {
	
	$.getJSON( "../src/mines.php?action=newGame&game=" + game, function( data ) {
		if(data['status'] == 'error')
			errorGame(data['error'], data['message']);
	});
}

function cashOut(user, token, game) {
	console.log("Cash out");
}

function errorGame(errorCode, errorMessage) {
	$("#messages").text("Error " + errorCode + ": " + errorMessage);
	$("#closeMessage").text("X");
	
	setInterval(function() { closeMessage(); }, 1000 * 10);
}

function closeMessage() {
	$("#messages").text("");
	$("#closeMessage").text("");
}