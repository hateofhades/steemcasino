var app = require('express')();
var http = require('http').Server(app);
var mysql = require('mysql');
var io = require('socket.io')(http);
var math = require('mathjs');
var randomstring = require('randomstring');
var sha = require('sha.js');

app.get('/', function(req, res){
  res.sendFile(__dirname + '/index.html');
});

setTimeout(changeState, rollTime);

var getBetsInterval;

var state = 0;
var lastRolls = [23, 11, 10, 22];
var timestamp = 0;

var rollTime = 10 * 1000;
var betTime = 60 * 1000;

var jackpotTime = 10 * 60 * 1000;

var jackpotGame;

var totalBetJackpot = 50;
var jackpotHash, jackpotSecret = "No secret", lastJackpotSecret;
var getJackpotTotalInterval = setInterval(getJackpotBets, 1000 * 5);
var jackpotTimeTick, jackpotTimeTickMax = 120;

var gameid;

var con = mysql.createConnection({
  host: "localhost",
  user: "root",
  pass: "",
  database: "steemcasino"
});

con.connect(function(err) {
	if (err) throw err;
  console.log("Bot is now connected to the database.");
  getGameid();
  createJackpotGame();
});

function getGameid() {
	con.query("SELECT * FROM info", function (err, result) {
		gameid = result[4].value;
		jackpotGame = result[6].value;
	});
}

function getBets() {
	con.query("SELECT * FROM roulette", function (err, result) {
		var redBet = 0, blackBet = 0, greenBet = 0;
		var redPlayers = [], blackPlayers = [], greenPlayers = [];
		for(var val of result) {
			if(val.beton == 1) {
				redBet += val.bet;
				redPlayers.push([val.player, val.bet]);
			} else if(val.beton == 2) {
				blackBet += val.bet;
				blackPlayers.push([val.player, val.bet]);
			} else if(val.beton == 3) {
				greenBet += val.bet;
				greenPlayers.push([val.player, val.bet]);
			}
		}
		io.sockets.emit('message', {
			messageType: 4,
			redBet: redBet,
			blackBet: blackBet,
			greenBet: greenBet,
			redPlayers: redPlayers,
			blackPlayers: blackPlayers,
			greenPlayers: greenPlayers,
		});
	});
}

function getJackpotBets() {
	jackpotTimeTick++;
	con.query("SELECT * FROM jackpot", function (err, result) {
		var totalBet = 0, playerBet = [];
		for(var val of result) {
			totalBet += val.bet;
			playerBet.push([val.player, val.bet]);
		}
		
		io.sockets.emit('message', {
			messageType: 5,
			totalBet: totalBet,
			playerBet: playerBet
		});
		if(totalBet >= totalBetJackpot)
			endJackpotGame(totalBet, playerBet);
		else if(jackpotTimeTick == jackpotTimeTickMax)
			endJackpotGame(totalBet, playerBet);
	});
}

function createJackpotGame() {
	
	console.log("\nCreating jackpot game.");
	
	getBetsInterval = setInterval(getBets, 1000 * 5);
	jackpotTimeTick = 0;
	jackpotGame++;
	con.query("TRUNCATE jackpot", function (err, result) {
	});
	con.query("UPDATE info SET value = 0 WHERE name = 'jackpotstate'", function (err, result) {
	});
	con.query("UPDATE info SET value = " + jackpotGame + " WHERE name = 'jackpotgame'", function (err, result) {
	});
	
	var winnerTicket = math.random(0, 500001);
	winnerTicket = math.floor(winnerTicket);
	
	lastJackpotSecret = jackpotSecret;
	
	jackpotSecret = winnerTicket + "-" + randomstring.generate(100);
	jackpotHash = sha('sha256').update(jackpotSecret).digest('hex');
	
	io.sockets.emit('message', {
		messageType: 6,
		currentHash: jackpotHash,
		lastSecret: lastJackpotSecret,
		jackpotTimeLeft: jackpotTimeTickMax * 5
	});
}

function endJackpotGame(totalBet, playerBet) {
	if(totalBet != 0) {
		clearInterval(getBetsInterval);
		
		con.query("UPDATE info SET value = 1 WHERE name = 'jackpotstate'", function (err, result) {
		});
		var ticketsPerSBD = 500000 / totalBet;
		var playerAndTickets = [];
		for(var val of playerBet) {
			playerAndTickets.push([val[0], Math.floor(val[1] * ticketsPerSBD)]);
		}
		
		var winningTicket = jackpotSecret.substr(0, jackpotSecret.indexOf('-'));
		var winner = "", tickets = 0;
		for(var val of playerAndTickets) {
			tickets += val[1];
			if(tickets >= winningTicket) {
				winner = val[0];
				break;
			}
		}
		
		if(tickets < winningTicket) {
			winningTicket -= (500000 - tickets);
			tickets = 0;
			for(var val of playerAndTickets) {
				tickets += val[1];
				if(tickets >= winningTicket) {
					winner = val[0];
					break;
				}
			}
		}
		
		con.query("SELECT * FROM users WHERE username = '" + winner + "'", function (err, result) {
			var balance = result[0].balance;
			var won = result[0].won;
			var losted = result[0].losted;
			
			var reward = totalBet * 99.5 / 100;
			
			balance += reward;
			won += reward;
			var winnerBet = 0;
			
			for(var val of playerBet) {
				if(val[0] == winner) {
					winnerBet = val[1];
					break;
				}
			}
			
			losted -= winnerBet;
			
			con.query("UPDATE users SET balance = '" + balance +"', won = '" + won + "', losted = '" + losted + "' WHERE username = '" + winner + "'", function (er, resul) {
				con.query("UPDATE history SET win = 1, reward = '" + reward + "' WHERE transType = '7' AND user1 = '" + winner + "' AND gameid = '" + jackpotGame + "'", function (e, resu) {
					setTimeout(createJackpotGame, 20 * 1000);
					io.sockets.emit('message', {
						messageType: 7,
						winner: winner,
						totalBet: reward,
						winningTicket: winningTicket,
						ticketsAndPlayers: playerAndTickets
					});
					console.log("Jackpot game ended, total bets: " + totalBet + " SBD.");
				});
			});
		});
	} else {
		createJackpotGame();
	}
}

io.on('connection', function(socket){
	socket.on('username', function(username) {
		console.log(username + " has connected.");
		socket.emit('message', {
			messageType: 1,
			state: state,
			timestamp: timestamp,
			lastRolls: lastRolls
		});
	});
});

http.listen(3000, function(){
  console.log('listening on *:3000');
});

function roll() {
	var rolled = math.random(0, 38);
	rolled = math.floor(rolled);
	
	console.log("Rolled: " + rolled);
	
	return rolled;
}

function changeState() {
	if(state) {
		state = 0;
	
		createGame();
		
		console.log("\nBetting round has started.");
		
		setTimeout(changeState, betTime);
		
		getBets();
		getBetsInterval = setInterval(getBets, 1000 * 5);
	} else {
		state = 1;
		
		clearInterval(getBetsInterval);
		getBets();
		
		var currRoll = roll();
		var color = calculateColor(currRoll);
		
		lastRolls.unshift(currRoll);
		if(lastRolls.length == 6)
			lastRolls.splice(-1, 1);
		
		console.log(lastRolls);
		
		win(color, currRoll);
		
		setTimeout(changeState, rollTime);
	}
}

function win(color, currRoll) {
	
	timestamp = Math.floor(Date.now() / 1000) + Math.floor(rollTime/1000);
	
	con.query("UPDATE info SET value = 1 WHERE name = 'roulettestate'", function (err, result) {
	});
	con.query("SELECT * FROM roulette WHERE beton = " + color, function (err, result) {
		for( var i = 0, len = result.length; i < len; i++ ) {
			var reward = result[i].bet;
			var bet = result[i].bet;
			var player = result[i].player;
			if(color == 1 || color == 2)
				reward = reward * 2;
			else
				reward = reward * 14;
			
			con.query("SELECT * FROM users WHERE username = '" + result[i].player + "'", function (err, resultd) {
				if(resultd) {
					var balance = resultd[0].balance;
					var won = resultd[0].won;
					var losted = resultd[0].losted;
					
					balance = balance + reward;
					won = won + reward;
					losted = losted - bet;
					
					con.query("UPDATE users SET losted = '" + losted + "', balance = '" + balance + "', won = '" + won + "' WHERE username = '" + player + "'", function (err, result) {
					});
					con.query("UPDATE history SET win = '1', reward = '" + reward +"' WHERE user1 = '" + player + "' AND transType = '6' AND gameid = '" + gameid + "'", function (err, result) {
					});
					
				}
			});
		}
	});
	
	io.sockets.emit('message', {
		messageType: 2,
		roll: currRoll,
		lastRolls: lastRolls,
		timestamp: rollTime / 1000
	});
}

function createGame() {
	
	timestamp = Math.floor(Date.now() / 1000) + Math.floor(betTime / 1000);
	
	gameid = gameid + 1;
	
	con.query("TRUNCATE roulette", function (err, result) {
	});
	con.query("UPDATE info SET value = 0 WHERE name = 'roulettestate'", function (err, result) {
	});
	con.query("UPDATE info SET value = " + Math.floor(Date.now() / 1000) + " WHERE name = 'roulettetimestamp'", function (err, result) {
	});
	con.query("UPDATE info SET value = " + gameid + " WHERE name = 'rouletteid'", function (err, result) {
	});
	
	io.sockets.emit('message', {
		messageType: 3,
		timestamp: betTime / 1000
	});
}

function calculateColor(currRoll) {
	if(currRoll == 1 || currRoll == 3 || currRoll == 5 || currRoll == 7 || currRoll == 9 || currRoll == 36 || currRoll == 34 || currRoll == 32 || currRoll == 30 || currRoll == 14 || currRoll == 16 || currRoll == 18 || currRoll == 12 || currRoll == 27 || currRoll == 23 || currRoll == 21 || currRoll == 19 || currRoll == 25)
		return 1;
	else if(currRoll == 13 || currRoll == 24 || currRoll == 15 || currRoll == 22 || currRoll == 17 || currRoll == 20 || currRoll == 11 || currRoll == 26 || currRoll == 28 || currRoll == 2 || currRoll == 35 || currRoll == 4 || currRoll == 33 || currRoll == 6 || currRoll == 31 || currRoll == 8 || currRoll == 29 || currRoll == 10)
		return 2;
	else if(currRoll == 0 || currRoll == 37)
		return 3;
}