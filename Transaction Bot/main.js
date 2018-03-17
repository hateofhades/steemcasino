var steem = require('steem');
var mysql = require('mysql');
var request = require('request');

var con = mysql.createConnection({
  host: "localhost",
  user: "root",
  pass: "",
  database: "steemcasino"
});

var lastTransInt;

var botName = "";
var activekey = "";
var depositMemo = "deposit";
var withdrawMemo = "withdraw";

con.connect(function(err) {
	if (err) throw err;
  console.log("Bot is now connected to the database.");
  lastTrans();
});

steem.api.setOptions({ url: 'https://api.steemit.com'});

setInterval(updateLastTrans, 5*1000);

function lastTrans() {	
	con.query("SELECT * FROM info", function (err, result) {
		if (err) throw err;
    console.log(result);
	console.log();
	lastTransInt = result[0].value;
  });
}

function updateLastTrans() {
	request("https://uploadbeta.com/api/steemit/transfer-history/?id=" + botName, function (error, response, body) {
		if (!error && response.statusCode == 200) {
			var json = JSON.parse(body);
			console.log("New transaction list aquired.");
			var newTransInt = Object.keys(json).length;
			var transNew = newTransInt - lastTransInt;
			
			lastTransInt = newTransInt;

			con.query("UPDATE info SET value = " + lastTransInt + " WHERE name = 'lastTrans'", function (err, result) {
				if (err) throw err;
			});
	
			console.log("Transactions updated. " + transNew + " new transactions. \n");
			
			if(transNew > 0)
				console.log("Transactions now: " + lastTransInt + "\n"); 
			
			for(var i = 0; i<= transNew - 1; i++)
			{
				if(!depositMemo.localeCompare(json[i].memo)) {
					var trans = json[i].transaction.split(" ");
					var username = trans[4];
					var deposit = parseFloat(trans[1]);
					var currency = trans[2];
					if(!currency.localeCompare("SBD"))
					{
						console.log("Registering deposit from: " + username);
						depositReceived(username, deposit);	
					}
				}
				else {
					var memed = json[i].memo.split(" ");
					if(!withdrawMemo.localeCompare(memed[0])) {
						var trans = json[i].transaction.split(" ");
						var username = trans[4];
						var withdraw = parseFloat(memed[1]);
						console.log(username + " wants to withdraw: " + withdraw + " SBD.");
						withdrawReceived(username, withdraw);
					}
				}
			}
		}
	});
}

function depositReceived(username, deposit) {
	con.query("SELECT * FROM users WHERE username = '" + username + "'", function (err, result) {
				if (err) throw err;
				var balance = result[0].balance;
				balance = balance + deposit;
				con.query("UPDATE users SET balance = '" + balance + "' WHERE username = '" + username + "'", function (errr, rresult) {
					console.log("Deposited");
				});
			});
}

function withdrawReceived(username, withdraw) {
	con.query("SELECT * FROM users WHERE username = '" + username + "'", function (err, result) {
		if (err) throw err;
		var balance = result[0].balance;
		if(balance >= withdraw) {
			var newBalance = balance - withdraw;
			
			steem.api.getAccounts([botName], function(err, result) {	
				var botBalance = result[0].sbd_balance.split(" ");
				botBalance = botBalance[0];
				botBalance = botBalance - 0.001;
				
				if(botBalance >= withdraw)
				{
					con.query("UPDATE users SET balance = '" + newBalance + "' WHERE username = '" + username + "'", function (errr, rresult) {
					
						steem.broadcast.transfer(activekey, botName, username, withdraw + " SBD", "Your withdrawal has been successful! New balance: " + newBalance + " SBD", function(err, result) {
							console.log(err, result);
							console.log(username + " has withdraw " + withdraw + " SBD."); 
					});
					
					});
				} else {
					steem.broadcast.transfer(activekey, botName, username, "0.001 SBD", "We don't have this amount of money at this moment. Please wait until we add more or withdraw less than: " + botBalance + " SBD", function(err, result) {
							console.log(err, result);
					});
				}
			});
			
		} else {
			console.log(username + " dosn't have enough money in balance. Balance: " + balance + ". Wants do withdraw: " + withdraw);
			steem.broadcast.transfer(activekey, botName, username, "0.001 SBD", "You dont have enough money in balance. Balance: " + balance + " SBD", function(err, result) {
				console.log(err, result);
			});
		}
	});		
}