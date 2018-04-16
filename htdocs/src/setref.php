<?php
include('db.php');
include('utils.php');

if(!isset($_GET['ref']) || $_GET['ref'] == "") {
	$arr = array('status' => 'error', 'error' => 200, 'message' => 'Invalid reffered.');
	echo json_encode($arr);
} else if(!IsLoggedOnUser()) {
	$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
	echo json_encode($arr);
} else {
	$query = $db->prepare("SELECT * FROM users WHERE username = ?");
	$query->bind_param('s', $_GET['ref']);
	
	$query->execute();
	$result = $query->get_result();
	if($result->num_rows) {
		if($_GET['ref'] == $_COOKIE['username']) {
			$arr = array('status' => 'error', 'error' => 201, 'message' => 'You can\'t reffer yourself.');
			echo json_encode($arr);
		} else {
		
			$quer = $db->prepare("SELECT * FROM users WHERE username = ?");
			$quer->bind_param('s', $_COOKIE['username']);
			
			$quer->execute();
			
			$res = $quer->get_result();
			
			if($res->num_rows) {
				while ($row = $res->fetch_assoc()) { 
						$ref = $row['reffered'];
					}
			}
			
			if(!$ref) {
				$quer = $db->prepare("UPDATE users SET reffered = ? WHERE username = ?");
				$quer->bind_param('ss', $_GET['ref'], $_COOKIE['username']);
				
				$quer->execute();
				
				$arr = array('status' => 'success', 'message' => $_GET['ref'].' has been added as your refferal.');
				echo json_encode($arr);
			} else {
				$arr = array('status' => 'error', 'error' => 202, 'message' => 'You already have a reffered.');
				echo json_encode($arr);
			}
		}
	} else {
		$arr = array('status' => 'error', 'error' => 200, 'message' => 'Invalid reffered.');
		echo json_encode($arr);
	}
}
?>