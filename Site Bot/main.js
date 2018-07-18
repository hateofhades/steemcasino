var app = require('express')();
var http = require('http').Server(app);
var mysql = require('mysql');
var io = require('socket.io')(http);
var math = require('mathjs');
var randomstring = require('randomstring');
var sha = require('sha.js');
var request = require('request');

app.get('/', function(req, res){
  res.sendFile(__dirname + '/index.html');
});

setTimeout(changeState, rollTime);

var getBetsInterval;

var state = 0;
var lastRolls = [11, 23, 11, 10, 22];
var timestamp = 0;

var rollTime = 10 * 1000;
var betTime = 30 * 1000;

var jackpotTime = 10 * 60 * 1000;

var jackpotGame;

var totalBetJackpot = 50;
var jackpotHash, jackpotSecret = "No secret", lastJackpotSecret = "No secret";
var rouletteHash, rouletteSecret = "No secret", lastRouletteSecret = "No secret", rouletteRoll = roll();
var getJackpotTotalInterval;
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
		var redBet = 0, blackBet = 0, greenBet = 0, customBet = 0;
		var redPlayers = [], blackPlayers = [], greenPlayers = [], customPlayers = [];
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
			} else {
				customBet += val.bet;
				customPlayers.push([val.player, val.bet, val.beton]);
			}
		}
		io.sockets.emit('message', {
			messageType: 4,
			redBet: redBet,
			blackBet: blackBet,
			greenBet: greenBet,
			customBet: customBet,
			redPlayers: redPlayers,
			blackPlayers: blackPlayers,
			greenPlayers: greenPlayers,
			customPlayers: customPlayers,
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
		if(totalBet >= totalBetJackpot) {
			endJackpotGame(totalBet, playerBet);
		}
		else if(jackpotTimeTick == jackpotTimeTickMax) {
			endJackpotGame(totalBet, playerBet);
		}
	});
}

function createJackpotGame() {
	
	console.log("\nCreating jackpot game.");
	
	clearInterval(getJackpotTotalInterval);
	getJackpotTotalInterval = setInterval(getJackpotBets, 1000 * 5);
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
		hash: jackpotHash,
		lastSecret: lastJackpotSecret,
		timeleft: jackpotTimeTickMax * 5,
		gameid: jackpotGame
	});
}

function endJackpotGame(totalBet, playerBet) {
	if(totalBet != 0) {
		clearInterval(getJackpotTotalInterval);
		
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
			var firstTickets = playerAndTickets[0][1];
			firstTickets += (500000 - tickets);
			playerAndTickets[0][1] = firstTickets;
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
						playerAndTickets: playerAndTickets
					});
					console.log("Jackpot game ended, total bets: " + totalBet + " SBD.");
					totalBet = 0;
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
			lastRolls: lastRolls,
			lastSecret: lastRouletteSecret,
			hash: rouletteHash
		});
		socket.emit('message', {
			messageType: 11,
			gameid: jackpotGame,
			timeleft: (jackpotTimeTickMax - jackpotTimeTick) * 5,
			hash: jackpotHash,
			lastSecret: lastJackpotSecret
		});
	});
	socket.on('sendMessage', function(content) {
		var cnt = content.split("|");
		var user = cnt[0], token = cnt[1], message = cnt[2];
		
		var url = "https://steemconnect.com/api/me?access_token=" + token;
		
		request({
			url: url,
			json: true
		}, function (error, response, body) {

			if (!error) {
				if(user == body['user']) {
					socket.emit('message', {
						messageType: "chat",
						user: user,
						content: message
					});
				}
			}
		});
	});
});

http.listen(3000, function(){
  console.log('listening on *:3000');
});

function roll() {
	var rolled = math.random(0, 38);
	
	rolled = math.floor(rolled);
	
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
		
		var color = calculateColor(rouletteRoll);
		
		lastRolls.unshift(rouletteRoll);
		if(lastRolls.length == 6)
			lastRolls.splice(-1, 1);
		
		console.log(lastRolls);
		
		var odd = 0, row = 0;
		
		if(rouletteRoll != 0 || rouletteRoll != 37) {
			if(rouletteRoll % 2)
				odd = 7;
			else if (!(rouletteRoll % 2))
				odd = 8;
			
			if(rouletteRoll <= 12)
				row = 4;
			else if(rouletteRoll <= 24)
				row = 5;
			else if(rouletteRoll <= 36)
				row = 6;
		}
		
		win(color, rouletteRoll, odd, row);
		
		setTimeout(changeState, rollTime);
	}
}

function win(color, rollRoulette, odd, row) {
	
	timestamp = Math.floor(Date.now() / 1000) + Math.floor(rollTime/1000);
	
	con.query("UPDATE info SET value = 1 WHERE name = 'roulettestate'", function (err, result) {
	});
	con.query("SELECT * FROM roulette WHERE beton = " + color + " OR beton = " + (100 + rollRoulette) + " OR beton = " + odd + " OR beton = " + row, function (err, result) {
		for( var i = 0, len = result.length; i < len; i++ ) {
			var reward = result[i].bet;
			var bet = result[i].bet;
			var betonb = result[i].beton;
			var player = result[i].player;
			if(betonb == 1 || betonb == 2 || betonb == 7 || betonb == 8)
				reward = reward * 2;
			else if(betonb == 3)
				reward = reward * 14;
			else if(betonb == 4 || betonb == 5 || betonb == 6)
				reward = reward * 3;
			else if(betonb >= 100 && betonb <= 137)
				reward = reward * 35;
			
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
	
	rouletteRoll = roll();
	var secretos = rouletteRoll + "-" + randomstring.generate(100);
	rouletteHash = sha('sha256').update(secretos).digest('hex');
	
	io.sockets.emit('message', {
		messageType: 2,
		roll: rollRoulette,
		lastRolls: lastRolls,
		timestamp: rollTime / 1000,
		lastSecret: rouletteSecret,
		hash: rouletteHash
	});
	
	lastRouletteSecret = rouletteSecret;
	rouletteSecret = secretos;
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

function calculateColor(rollRoulette) {
	if(rollRoulette == 1 || rollRoulette == 3 || rollRoulette == 5 || rollRoulette == 7 || rollRoulette == 9 || rollRoulette == 36 || rollRoulette == 34 || rollRoulette == 32 || rollRoulette == 30 || rollRoulette == 14 || rollRoulette == 16 || rollRoulette == 18 || rollRoulette == 12 || rollRoulette == 27 || rollRoulette == 23 || rollRoulette == 21 || rollRoulette == 19 || rollRoulette == 25)
		return 1;
	else if(rollRoulette == 13 || rollRoulette == 24 || rollRoulette == 15 || rollRoulette == 22 || rollRoulette == 17 || rollRoulette == 20 || rollRoulette == 11 || rollRoulette == 26 || rollRoulette == 28 || rollRoulette == 2 || rollRoulette == 35 || rollRoulette == 4 || rollRoulette == 33 || rollRoulette == 6 || rollRoulette == 31 || rollRoulette == 8 || rollRoulette == 29 || rollRoulette == 10)
		return 2;
	else if(rollRoulette == 0 || rollRoulette == 37)
		return 3;
}