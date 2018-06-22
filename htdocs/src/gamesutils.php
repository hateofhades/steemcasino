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
function dicesPick($seed, $secret) {
	$result = hash_hmac('sha512', $seed, $secret);
	
	$found = 0;
	$new = 0;
	
	for($i = 5; $i < 128; $i += 5) {
		$new = substr($result, ($i - 5), $i);
		$new = base_convert($new, 16, 10);
		if($new <= 999999) {
			$found = 1;
			break;
		}
	}
	
	if($found == 0)
		$new = 4500;
	else
		$new /= 100;
	
	return floor($new);
}

//This is the random picker for slots.
function slotPick($seed, $secret) {
	$result = hash_hmac('sha512', $seed, $secret);
	
	$found = 0;
	$new = 0;
	
	for($i = 5; $i < 128; $i += 5) {
		$new = substr($result, ($i - 5), $i);
		$new = base_convert($new, 16, 10);
		if($new <= 1000000) {
			$found = 1;
			break;
		}
	}
	
	if($found == 0)
		$new = 1;
	else
		$new /= 10000;
	
	$pick = floor($new);
	
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

//This is the function that creates a blackjack deck of cards and shuffles it.
function createDeck ($seed) {
	
	$deck = [];
	
	for($i = 2; $i <= 14; $i++ ) {
		array_push($deck, [$i, "A"]);
		array_push($deck, [$i, "B"]);
		array_push($deck, [$i, "C"]);
		array_push($deck, [$i, "D"]);
		
	}
	$deck = shuffleDeck($deck, $seed);
	return $deck;
}

//This is the function that shuffles the deck with a given seed so it's provable fair.
function shuffleDeck(&$items, $seed)
{
    @mt_srand($seed);
    for ($i = count($items) - 1; $i > 0; $i--)
    {
        $j = @mt_rand(0, $i);
        $tmp = $items[$i];
        $items[$i] = $items[$j];
        $items[$j] = $tmp;
    }
	
	return $items;
}

//This function draws the cards and returns them. *removeCards should be used after calling this function to remove them from the deck*
function drawCards($deck, $howmany = 1, $hand = []) {
	for($i = 0; $i < $howmany; $i++)
		array_push($hand, $deck[$i]);
	
	return $hand;
}

//This function is called when player pressed stand or hit 21 and house has under 17 points and returns a new house hand once it hit over 17 or 17 points.
function drawHouse($house, $deck) {
	$house = drawCards($deck, 1, $house);
	$deck = removeCards($deck);
	
	if(checkPoints($house) >= 17)
		return $house;
	else
		return drawHouse($house, $deck);
}

//This function checks if the hand (player or house) is over 21
function checkPoints($hand) {
	$points = 0;
	$aces = 0;
	
	for($i = 0; $i < count($hand); $i++) {
		if($hand[$i][0] <= 10)
			$points += $hand[$i][0];
		else if($hand[$i][0] > 11)
			$points += 10;
		else if($hand[$i][0] == 11)
			$aces++;
	}
	
	if($aces == 1) {
		if($points + 11 <= 21)
			$points += 11;
		else
			$points += 1;
	} else if($aces == 2) {
		if($points + 12 <= 21)
			$points += 12;
		else
			$points += 2;
	} else if($aces == 3) {
		if($points + 13 <= 21)
			$points += 13;
		else
			$points += 3;
	} else if($aces == 4) {
		if($points + 14 <= 21)
			$points += 14;
		else
			$points += 4;
	}
	
	return $points;
}

//This function removes the cards and returns the new deck.
function removeCards($deck, $howmany = 1) {
	for($i = 0; $i < $howmany; $i++)
		unset($deck[$i]);
	
	$deck = array_values($deck);
	
	return $deck;
}

?>