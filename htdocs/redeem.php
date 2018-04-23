<?php
include('src/db.php');
include('src/utils.php');

if(!isset($_GET['code']) || $_GET['code'] == "") {
	header("Location:index.php");
	die("Invalid code.");
} else if(!IsLoggedOnUser()) {
	header("Location:index.php");
	die("Not logged in.");
} else {
	$type = 1;
	
	$query = $db->prepare('SELECT * FROM promocodes WHERE name = ? AND type = ?');
	$query->bind_param('si', $_GET['code'], $type);
	
	$query->execute();
			
	$result = $query->get_result();
	if($result->num_rows) {
		while ($row = $result->fetch_assoc()) {
			$amount = $row['amount'];
		}
		$type = 2;
		
		$query = $db->prepare('SELECT * FROM promocodes WHERE name = ? AND usedCode = ? AND type = ?');
		$query->bind_param('ssi', $_COOKIE['username'], $_GET['code'], $type);
	
		$query->execute();
		$result = $query->get_result();
		
		if($result->num_rows) {
			header("Location:index.php");
			die("Already redeemed.");
		} else {
			$query = $db->prepare('SELECT * FROM users WHERE username = ?');
			$query->bind_param('s', $_COOKIE['username']);
	
			$query->execute();
			
			$result = $query->get_result();
			
			if($result->num_rows) {
				while ($row = $result->fetch_assoc()) {
					$balance = $row['balance'];
				}
				
				$balance += $amount;
				
				$query = $db->prepare('UPDATE users SET balance = ? WHERE username = ?');
				$query->bind_param('ds', $balance, $_COOKIE['username']);
	
				$query->execute();
				
				$type = 2;
				
				$query = $db->prepare('INSERT INTO promocodes (type, name, usedCode) VALUES (?, ?, ?)');
				$query->bind_param('iss', $type, $_COOKIE['username'], $_GET['code']);
	
				$query->execute();
				
				header("Location:index.php");
				die("Code successfully redeemed.");
			} else {
				header("Location:index.php");
				die("User does not exist in the database.");
			}
		}
	} else {
		header("Location:index.php");
		die("Promocode does not exist.");
	}
}
?>