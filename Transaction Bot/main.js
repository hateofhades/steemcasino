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

var username = "";
var activekey = "";
var depositMemo = "deposit";

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
	request("https://uploadbeta.com/api/steemit/transfer-history/?id=" + username, function (error, response, body) {
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