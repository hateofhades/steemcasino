<?php
include('db.php');
include('utils.php');

if(!isset($_GET['type']) || $_GET['type'] == "") {
	$arr = array('status' => 'error', 'error' => 220, 'message' => 'Invalid game type.');
	echo json_encode($arr);
} else if(!isset($_GET['game']) || $_GET['game'] == "") {
	$arr = array('status' => 'error', 'error' => 230, 'message' => 'Invalid game id.');
	echo json_encode($arr);
} else if(!IsLoggedOnUser()) {
	$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
	echo json_encode($arr);
} else {
	if($_GET['type'] == 1) {
		$query = $db->prepare("SELECT * FROM coinflip WHERE ID = ?");
		$query->bind_param('i', $_GET['game']);
	
		$query->execute();
		$result = $query->get_result();
		if($result->num_rows) {
			while ($row = $result->fetch_assoc()) { 
				$user1 = $row['player1'];
				$user2 = $row['player2'];
				$bet = $row['bet'];
			}
			if($user1 == "" || $user2 == "") {
				if($user1 == $_COOKIE['username'] || $user2 == $_COOKIE['username']) {
					$q = $db->prepare("SELECT * FROM users WHERE username = ?");
					$q->bind_param('s', $_COOKIE['username']);
					
					$q->execute();
					$r = $q->get_result();
					while($row = $r->fetch_assoc()) {
						$balance = $row['balance'];
					}
					
					$balance += $bet;
					
					$q = $db->prepare("UPDATE users SET balance = ? WHERE username = ?");
					$q->bind_param('ds', $balance, $_COOKIE['username']);
					
					$q->execute();
					
					$q = $db->prepare("DELETE FROM coinflip WHERE ID = ?");
					$q->bind_param('i', $_GET['game']);
					
					$q->execute();
					
					$arr = array('status' => 'success');
					echo json_encode($arr);
				}
			}
		}
	} else if($_GET['type'] == 2) {
		$query = $db->prepare("SELECT * FROM rps WHERE ID = ?");
		$query->bind_param('i', $_GET['game']);
	
		$query->execute();
		$result = $query->get_result();
		if($result->num_rows) {
			while ($row = $result->fetch_assoc()) { 
				$user1 = $row['player1'];
				$user2 = $row['player2'];
				$bet = $row['bet'];
			}
			if($user1 == "" || $user2 == "") {
				if($user1 == $_COOKIE['username'] || $user2 == $_COOKIE['username']) {
					$q = $db->prepare("SELECT * FROM users WHERE username = ?");
					$q->bind_param('s', $_COOKIE['username']);
					
					$q->execute();
					$r = $q->get_result();
					while($row = $r->fetch_assoc()) {
						$balance = $row['balance'];
					}
					
					$balance += $bet;
					
					$q = $db->prepare("UPDATE users SET balance = ? WHERE username = ?");
					$q->bind_param('ds', $balance, $_COOKIE['username']);
					
					$q->execute();
					
					$q = $db->prepare("DELETE FROM rps WHERE ID = ?");
					$q->bind_param('i', $_GET['game']);
					
					$q->execute();
					
					$arr = array('status' => 'success');
					echo json_encode($arr);
				}
			}
		}
	}
}
?>