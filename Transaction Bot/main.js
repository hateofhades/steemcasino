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

var isMaintenance = false;

con.connect(function(err) {
	if (err) throw err;
  console.log("Bot is now connected to the database.");
  lastTrans();
});

steem.api.setOptions({ url: 'https://api.steemit.com'});

setInterval(updateLastTrans, 5*1000);
maintenanceCheck();
var maintenanceCheckID = setInterval(maintenanceCheck, 60*1000*30);

function lastTrans() {	
	con.query("SELECT * FROM info", function (err, result) {
		if (err) throw err;
    console.log(result);
	console.log();
	lastTransInt = result[0].value;
  });
}

function maintenanceCheck() {
	con.query("SELECT * FROM info WHERE name = 'isMaintenance'", function (err, result) {
		if(result[0].value == 1 && isMaintenance == false)
		{
			isMaintenance = true;
			console.log("SteemCasino is now in maintenance mode!");
			clearInterval(maintenanceCheckID);
			var maintenanceCheckID = setInterval(maintenanceCheck, 60*1000*5);
		}
		else if(result[0].value == 0 && isMaintenance == true) {
			isMaintenance = false;
			console.log("SteemCasino is not anymore in maintenance mode!");
			clearInterval(maintenanceCheckID);
			var maintenanceCheckID = setInterval(maintenanceCheck, 60*1000*30);
		}
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
						if(isMaintenance == false)
						{
							console.log("Registering deposit from: " + username);
							depositReceived(username, deposit);	
						} else {
							console.log(username + " send a deposit in SBD, but SteemCasino is in maintenance mode, returning.");
							returnDeposit(username, deposit, "SBD");
						}
					}
					else
					{
						console.log(username + " send a deposit in STEEM, returning.");
						returnDeposit(username, deposit, "STEEM");
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

function returnDeposit(username, deposit, currency) {
	if(currency == "STEEM")
		var returnMemo = "We currently do not accept deposits in STEEM only SBD.";
	else
		var returnMemo = "We are currently performing maintenance!";
	
	steem.broadcast.transfer(activekey, botName, username, deposit + " " + currency, returnMemo, function(err, result) {
		console.log("Returned to " + username + " " + deposit + " " + currency);
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
							console.log(username + " has withdraw " + withdraw + " SBD."); 
					});
					
					});
				} else {
					steem.broadcast.transfer(activekey, botName, username, "0.001 SBD", "We don't have this amount of money at this moment. Please wait until we add more or withdraw less than: " + botBalance + " SBD", function(err, result) {
					});
				}
			});
			
		} else {
			console.log(username + " dosn't have enough money in balance. Balance: " + balance + ". Wants do withdraw: " + withdraw);
			steem.broadcast.transfer(activekey, botName, username, "0.001 SBD", "You dont have enough money in balance. Balance: " + balance + " SBD", function(err, result) {
			});
		}
	});		
}