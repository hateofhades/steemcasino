# SteemCasino - Coming Soon 
- Blackjack
- Poker
- Coinflip
- Roulette
- Rock, Paper, Scissors
- And more...

# Installation
```
git clone https://github.com/andreistalker/steemcasino.git
cd steemcasino/Transaction Bot
$ npm install mysql
$ npm install request
$ npm install steem

```

Import the .sql file into your database
Copy the htdocs folder in your Apache/Xampp/etc

Modify the first lines of Transaction Bot/main.js with your information
```
var con = mysql.createConnection({
  host: "localhost",
  user: "root",
  pass: "",
  database: "steemcasino"
});
```
```
var botName = "";
var activekey = "";
```

Modify htdocs/src/db.php with your database info.
```
$host = "localhost";
$user = "root";
$pass = "";
$database = "steemcasino";
```

Modify htdocs/src/config.php with your website address and bot name.
```
$botaccount = "";
$websiteadress = "http://localhost";
```

Modify htdocs/js/config.js with your website address
```
var sc2CallbackURL = 'http://localhost/loggedin.php';
```

Modify htdocs/js/sc2.js with your SteemConnect App Info
```
sc2.init(
{
	app: 'your-app-name-here',
	callbackURL: sc2CallbackURL,
	scope: ['login'],
});
```

Run the transaction bot.
```
$ node main.js
```

Start the Apache server.

You're all set up!

