<?php
function generateSecret($length = 99) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $ab = mt_rand(0, 100);
	
	if($ab % 2)
		$randomString = "A";
	else
		$randomString = "B";
	
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
?>