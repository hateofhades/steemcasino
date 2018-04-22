<?php
include('db.php');
include('utils.php');

if(!IsLoggedOnUser()) {
	$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
	echo json_encode($arr);
} else {
	$privacy = 1;
	$quer = $db->prepare("UPDATE users SET privacy = ? WHERE username = ?");
	$quer->bind_param('is', $privacy, $_COOKIE['username']);
	
	$quer->execute();
	
	setcookie("privacy", 1, $_COOKIE['expires_in'], "/");
	
	$arr = array('status' => 'success', 'message' => 'The Privacy Policy has been accepted.');
	echo json_encode($arr);		
}
?>