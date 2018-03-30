var game = 0;
var timer, reward, hash;

$("#table").hide();

function newGame(game, bet) {
	$("#messages-box").css('background-color', 'yellow');
	$("#messages").text("Working...");
	$("#closeMessage").text("X");
			
	clearInterval(timer);
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
	
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
			
			for(var i = 1; i<=25; i++) {
				$("#a" + i).attr("onClick", "hitBlock(" + game + ", " + i + ");");
				$("#" + i).css("background-color", "pink");
			}
			
			$("#messages-box").css('background-color', 'green');
			$("#messages").text("A new game has started.");
			$("#hash").text("Hash: " + hash);
			$("#cashout").attr("onClick", "cashOut(" + game + ");");
			$("#newgame").attr("onClick", "newGame(" + game + ", bet);");
			$("#closeMessage").text("X");
			
			$("#secret").text("");
			
			$("#bet").hide();
			$("#betn").hide();
			$("#table").show();
	
			clearInterval(timer);
			timer = setInterval(function() { closeMessage(); }, 1000 * 10);
		}
	});
}

function cashOut(game) {
	$("#messages-box").css('background-color', 'yellow');
	$("#messages").text("Working...");
	$("#closeMessage").text("X");
			
	clearInterval(timer);
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
	
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
			
			for(var i = 1; i<=25; i++)
				$("#a" + i).attr("onClick", "");

			
			$("#table").hide();
			
			$("#bet").show();
			$("#betn").show();
			
			$("#secret").text("" + data['secret']);
			
			$("#messages-box").css('background-color', 'green');
			$("#messages").text("" + data['message']);
			$("#closeMessage").text("X");
			
			clearInterval(timer);
			timer = setInterval(function() { closeMessage(); }, 1000 * 10);
		}
	});
}

function hitBlock(game, block) {
	$("#messages-box").css('background-color', 'yellow');
	$("#messages").text("Working...");
	$("#closeMessage").text("X");
			
	clearInterval(timer);
	timer = setInterval(function() { closeMessage(); }, 1000 * 10);
	
	$.getJSON( "../src/mines.php?action=hitBlock&game=" + game + "&block=" + block, function( data ) {
		console.log(data);
		if(data['status'] == 'error')
			errorGame(data['error'], data['message']);
		if(data['status'] == 'lost') {
			$("#messages-box").css('background-color', 'red');
			$("#messages").text(data['message']);
			$("#closeMessage").text("X");
			
			$("#cashout").text("");
			game = 0;
			$("#newgame").text("Start new game");
			
			$("#cashout").attr("onClick", "cashOut(0);");
			$("#newgame").attr("onClick", "newGame(0, bet);");
			
			var bombs = data['bombs'];
			
			bombs.forEach(changeTable);
			
			$("#bet").show();
			$("#betn").show();
			
			$("#secret").text("Secret: " + data['secret']);
	
			clearInterval(timer);
			timer = setInterval(function() { closeMessage(); }, 1000 * 10);
		} else if(data['status'] == 'increase') {
			$("#" + data['block']).css('background-color', 'green');
			
			$("#a" + data['block']).attr("onClick", "");
			
			$("#messages-box").css('background-color', 'green');
			$("#messages").text(data['message']);
			$("#closeMessage").text("X");
	
			clearInterval(timer);
			timer = setInterval(function() { closeMessage(); }, 1000 * 10);
		}
			
	});
}

function changeTable(element, index, array) {
	$("#" + element).css('background-color', 'red');
	$("#a" + element).attr("onClick", "");
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