<?php

//We are generating a secret for Coinflip - if the first character is A player1 wins if it's B player2 wins.
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

//We are generating a secret for mines.
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

//This is the random picker for dices.
function dicesPick() {
	return mt_rand(1, 10000);
}

//This is the random picker for slots.
function slotPick() {
	$pick = mt_rand(1, 100);
	
	if($pick <= 30)
		return 0;
	else if($pick <= 60)
		return 3;
	else if($pick <= 75)
		return 2;
	else if($pick <= 90)
		return 1;
	else if($pick <= 97)
		return 4;
	else
		return 5;
}

//This function determinates if the player wins at slots.
function slotIsWin($slot1, $slot2, $slot3) {
	if($slot1 == 0 && $slot2 == 0 && $slot3 == 0)
		return 2;
	else if($slot1 == 1 && $slot2 == 1 && $slot3 == 1)
		return 2;
	else if($slot1 == 2 && $slot2 == 2 && $slot3 == 2)
		return 2;
	else if(($slot1 == 0 || $slot1 == 1 || $slot1 == 2) && ($slot2 == 0 || $slot2 == 1 || $slot2 == 2) && ($slot3 == 0 || $slot3 == 1 || $slot3 == 2))
		return 1;
	else if($slot1 == 3 && $slot2 == 3 && $slot3 == 3)
		return 3;
	else if($slot1 == 4 && $slot2 == 4 && $slot3 == 4)
		return 5;
	else if($slot1 == 5 && $slot2 == 5 && $slot3 == 5)
		return 6;
	else if($slot1 == 3 && $slot2 == 4 && $slot3 == 5 || $slot1 == 3 && $slot2 == 5 && $slot3 == 4 || $slot1 == 4 && $slot2 == 3 && $slot3 == 5 || $slot1 == 4 && $slot2 == 5 && $slot3 == 3 || $slot1 == 5 && $slot2 == 4 && $slot3 == 3 || $slot1 == 5 && $slot2 == 3 && $slot3 == 4)
		return 4;
	else
		return 0;
}
?>