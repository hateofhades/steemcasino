<?php
include('utils.php');
include('gamesutils.php');
include('db.php');

if(!isset($_GET['betOn']) || $_GET['betOn'] == "" || !isset($_GET['bet']) || $_GET['bet'] == "") {
	$arr = array('status' => 'error', 'error' => 600, 'message' => 'Invalid bet.');
	echo json_encode($arr);
}
?>