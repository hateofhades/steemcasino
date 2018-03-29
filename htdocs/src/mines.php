<?php
include('utils.php');

if(!isset($_GET['action']) || $_GET['action'] == "") {
	$arr = array('status' => 'error', 'error' => 500, 'message' => 'Action is not set.');
	echo json_encode($arr);
} else if ($_GET['action'] == "newGame") {
	if(!isset($_GET['game'])) {
		$arr = array('status' => 'error', 'error' => 501, 'message' => 'Game is not set.');
		echo json_encode($arr);
	} else if (!IsLoggedOnUser()) {
		$arr = array('status' => 'error', 'error' => 502, 'message' => 'Session is invalid. Please reload.');
		echo json_encode($arr);
	} else if($_GET['game'] != 0) {
		$arr = array('status' => 'error', 'error' => 503, 'message' => 'A game is already running. Please cash out to start another game.');
		echo json_encode($arr);
	} else {
		
	}
}
?>