<?php
include_once('src/db.php');
include_once('src/utils.php');

$refby = "none";

if(IsLoggedOnUser()) {
	$balance = 0;
	$query = $db->prepare('SELECT * FROM users WHERE username = ?');
	$query->bind_param('s', $_COOKIE['username']);
		
	$query->execute();
		
	$result = $query->get_result();
	while ($row = $result->fetch_assoc()) {
		$balance = $row['balance'];
		$won = $row['won'];
		$lost = $row['losted'];
		
		$reffered = $row['reffered'];

		$profit = $won - $lost;
	}
	
	if($reffered) {
		$refby = $reffered;
	}

	$query = $db->prepare('SELECT * FROM history WHERE user1 = ? OR user2 = ?');
	$query->bind_param('ss', $_COOKIE['username'], $_COOKIE['username']);

	$query->execute();
	
	$result = $query->get_result();
	$history = "<br>";
	while ($row = $result->fetch_assoc()) {
		if($row['transType'] == 1) {
			$date = date("F j, Y, g:i a T", $row['timestamp']);
			$history =  "
			<h4 style=\"display:inline\">Deposit | </h4><h4 style=\"display:inline;color:green\">+".$row['amount']." SBD</h4> | <h4 style=\"display:inline\">".$date."</h4><br>
			".$history;
		} else if($row['transType'] == 2) {
			$date = date("F j, Y, g:i T", $row['timestamp']);
			$history =  "
			<h4 style=\"display:inline\">Withdraw | </h4><h4 style=\"display:inline;color:red\">-".$row['amount']." SBD</h4> | <h4 style=\"display:inline\">".$date."</h4><br>
			".$history;
		} else if($row['transType'] == 3) {
			$date = date("F j, Y, g:i a T", $row['timestamp']);
			if($row['user1'] == $_COOKIE['username'])
				if($row['win'] == 1)
					$win = "
					<h4 style=\"display:inline;color:green\">+".$row['reward']." SBD</h4>
				";
				else
					$win = "
					<h4 style=\"display:inline;color:red\">-".$row['amount']." SBD</h4>
				";
			else if($row['user2'] == $_COOKIE['username'])
				if($row['win'] == 1)
					$win = "
					<h4 style=\"display:inline;color:red\">-".$row['amount']." SBD</h4>
				";
				else 
					$win = "
					<h4 style=\"display:inline;color:green\">+".$row['reward']." SBD</h4>
				";
			$history =  "
			<h4 style=\"display:inline\">Coinflip #".$row['gameid']." | ".$row['user1']." vs ".$row['user2']." |</h4> ".$win." | <h4 style=\"display:inline\">".$date."</h4><br>
			".$history;
		} else if($row['transType'] == 4) {
			$date = date("F j, Y, g:i a T", $row['timestamp']);
			if($row['user1'] == $_COOKIE['username'])
				if($row['win'] == 1)
					$win = "
					<h4 style=\"display:inline;color:green\">+".$row['reward']." SBD</h4>
				";
				else
					$win = "
					<h4 style=\"display:inline;color:red\">-".$row['amount']." SBD</h4>
				";
			else if($row['user2'] == $_COOKIE['username'])
				if($row['win'] == 1)
					$win = "
					<h4 style=\"display:inline;color:red\">-".$row['amount']." SBD</h4>
				";
				else 
					$win = "
					<h4 style=\"display:inline;color:green\">+".$row['reward']." SBD</h4>
				";
			$history =  "
			<h4 style=\"display:inline\">Rock Paper Scissors #".$row['gameid']." | ".$row['user1']." vs ".$row['user2']." |</h4> ".$win." | <h4 style=\"display:inline\">".$date."</h4><br>
			".$history;
		} else if($row['transType'] == 5) {
			$date = date("F j, Y, g:i a T", $row['timestamp']);
			if($row['win'] == 1)
				$win = "
					<h4 style=\"display:inline;color:green\">+".$row['reward']." SBD</h4>
				";
			else
				$win = "
					<h4 style=\"display:inline;color:red\">-".$row['amount']." SBD</h4>
				";
			$history =  "
			<h4 style=\"display:inline\">Mines #".$row['gameid']." | </h4> ".$win." | <h4 style=\"display:inline\">".$date."</h4><br>
			".$history;
		} else if($row['transType'] == 6) {
			$date = date("F j, Y, g:i a T", $row['timestamp']);
			if($row['win'] == 1)
				$win = "
					<h4 style=\"display:inline;color:green\">+".$row['reward']." SBD</h4>
				";
			else
				$win = "
					<h4 style=\"display:inline;color:red\">-".$row['amount']." SBD</h4>
				";
			$history =  "
			<h4 style=\"display:inline\">Roulette #".$row['gameid']." | </h4> ".$win." | <h4 style=\"display:inline\">".$date."</h4><br>
			".$history;
		} else if($row['transType'] == 7) {
			$date = date("F j, Y, g:i a T", $row['timestamp']);
			if($row['win'] == 1)
				$win = "
					<h4 style=\"display:inline;color:green\">+".$row['reward']." SBD</h4>
				";
			else
				$win = "
					<h4 style=\"display:inline;color:red\">-".$row['amount']." SBD</h4>
				";
			$history =  "
			<h4 style=\"display:inline\">Jackpot #".$row['gameid']." | </h4> ".$win." | <h4 style=\"display:inline\">".$date."</h4><br>
			".$history;
		}
	}
	
	$yr = "";
	
	$query = $db->prepare('SELECT * FROM users WHERE reffered = ?');
	$query->bind_param('s', $_COOKIE['username']);
		
	$query->execute();
		
	$result = $query->get_result();
	while ($row = $result->fetch_assoc()) {
		$yr = "<h4 style=\"display:inline\">".$row['username']."</h4>".$yr;
	}
}
?>
<html>
	<head>
		<?php include('src/head.php'); ?>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<center>
			<div class="default-body">
				<div id="messages-box">
					<p id="messages" style="display:inline"></p>
					<a href="#" id="closeMessage" onclick="closeMessage()"></a>
				</div>
				<div style="margin-top:2%;">
					<img id="accountPicture" style="display:none;margin-right:2%;margin-top:5px;vertical-align:middle" width="60px" height="60px"><h1 id="accountName" style="vertical-align:middle;display:inline;margin-top:0;">Loading...</h1>
				</div>
				<br>
				<script>
				sc2.me(function (err, result)
				{
					if (!err)
					{
						console.log(result);
						$("#accountName").text(Cookies.get("username") + " (");
						var reputation = result.account.reputation;
						var profileImage = "https://steemitimages.com/u/" + Cookies.get("username") + "/avatar";
						$("#accountPicture").attr("src",profileImage);
						$("#accountPicture").css("border-radius", "60px");
						$("#accountPicture").css("display", "inline");
						console.log(profileImage);
						reputation = log10(reputation);
						reputation = reputation - 9;
						if(reputation > 0) {
							reputation = reputation * 9;
							reputation = reputation + 25;
						}
						reputation = Math.floor(reputation);
						$("#accountName").append(reputation + ")");
						$("#balance").text("Balance: <?php echo $balance;?> SBD ");
						$("#balanced").text("Balance: <?php echo $balance;?> SBD ");
						$("#topup").text(" Deposit");
						$("#withdraw").text(" Withdraw");
						$("#lll").text(" / ");
						$("#profit").text("Profit: <?php echo $profit." SBD";?>");
						$("#totals").text("<?php echo "(Wins: ".$won." SBD | Loses: ".$lost." SBD )";?>");
						$("#history").text("History");
						$("#historyTable").css("display", "");
						var ref = "<?php echo $refby; ?>";
						
						if(ref == "none") {
							$("#reftext").css("display", "");
							$("#refsub").css("display", "");
						}
						
						$("#refTable").css("display", "");
						$("#ref").text("Refferals");
						$("#by").text("Reffered by <?php echo $refby;?>");
						$("#refs").text("Your refferals");
					}
				});
				
				function setRef() {
					var value = $("#reftext").val();
					$.getJSON( "../src/setref.php?ref=" + value, function( data ) {
						if(data['status'] == 'success') {
							message(data['message']);
						} else {
							error(data['error'], data['message']);
						}						
					});
				}
				
				var timer;
				
				function closeMessage() {
				clearInterval(timer);
				$("#messages").text("");
				$("#closeMessage").text("");
			}
			
			function message(messaged) {
				$("#messages-box").css('background-color', 'green');
				$("#messages").text(messaged);
				$("#closeMessage").text("X");
				
				clearInterval(timer);
				timer = setInterval(function() { closeMessage(); }, 1000 * 10);
			}
			
			function error(errorCode, errorMessage) {
				$("#messages-box").css('background-color', 'red');
				$("#messages").text("Error " + errorCode + ": " + errorMessage);
				$("#closeMessage").text("X");
				
				clearInterval(timer);
				timer = setInterval(function() { closeMessage(); }, 1000 * 10);
			}
				</script></h1>
				<h3 id="balanced" style="display:inline"></h3>
				<a href="#" id="topup"  onClick="MyWindow=window.open('balance.php?action=deposit','MyWindow',width=600,height=300); return false;"></a> 
				<p id="lll" style="display:inline"></p> 
				<a href="#" id="withdraw" onClick="MyWindow=window.open('balance.php?action=withdrawal','MyWindow',width=600,height=300); return false;"></a>
				<h3 id="profit" style="margin-bottom:0"></h3>
				<h5 id="totals" style="margin-top:0"></h4>
				<h2 id="ref" style="text-decoration:underline"></h2>
				<h5 id="by" style="margin-top:0;margin-bottom:0"></h5>
				<input id="reftext" type="text" style="display:none"></input>
				<input id="refsub" type="submit" onClick="setRef()" style="display:none"></input><br><br>
				<h4 id="refs" style="margin-top:0;text-decoration:underline"></h5>
				<div id="refTable" style="display:none">
					<?php echo $yr;?>
				</div>
				<h2 id="history" style="text-decoration:underline"></h2>
				<div id="historyTable" style="display:none">
					<?php echo $history;?>
				</div>
		</center>
		<?php include('src/footer.php'); ?>
	</body>
</html>