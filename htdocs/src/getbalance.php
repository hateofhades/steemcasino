<?php
include('utils.php');
include('gamesutils.php');
include('db.php');

if(IsLoggedOnUser()) {
	$query = $db->prepare('SELECT * FROM users WHERE username = ?');
	$query->bind_param('s', $_COOKIE['username']);
	
	$query->execute();
	$result = $query->get_result();
	if($result->num_rows) {
		while ($row = $result->fetch_assoc()) {
			$balance = $row['balance'];
			$balance += $row['promob'];
			$arr = array('status' => 'success', 'balance' => $balance);
			echo json_encode($arr);
		}
	}
} else {
	$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
	echo json_encode($arr);
}
?>