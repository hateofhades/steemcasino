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

function generateSecretMines($mode = 1, $length = 92) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
	
	if($mode == 1) {
		$mine1 = mt_rand(1, 25);
		$mine2 = mt_rand(1, 25);
		$mine3 = mt_rand(1, 25);
		
		if($mine1 == $mine2 || $mine2 == $mine3 || $mine1 == $mine3) {
			return generateSecretMines($mode, $length);
		}		
		
		$randomString = $mine1."-".$mine2."-".$mine3."-";
	}
	
	for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

	return $randomString;
}
?>