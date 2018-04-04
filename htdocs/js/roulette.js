function getUnder() {
	var currentUrl = window.location.href;
	var under = 0;
	notPhp = currentUrl.indexOf("#");
	
	if(notPhp != -1) {
		currentUrl = currentUrl.substring(currentUrl.indexOf("#")+1);
		
		if(currentUrl > 36 && currentUrl != 0 || currentUrl == "" || currentUrl == "00")
			under = 0;
		else if(currentUrl == "zz")
			under = 37;
		else
			under = currentUrl;
	} else 
		under = 0;
	
	return under;
}

function calculateZero() {
	var under = getUnder();
	var returned;
	
	if(under == 0)
		returned = 0;
	else if(under == 1)
		returned = 38;
	else if(under == 13)
		returned = 37;
	else if(under == 36)
		returned = 36;
	else if(under == 24)
		returned = 35;
	else if(under == 3)
		returned = 34;
	else if(under == 15)
		returned = 33;
	else if(under == 34)
		returned = 32;
	else if(under == 22)
		returned = 31;
	else if(under == 5)
		returned = 30;
	else if(under == 17)
		returned = 29;
	else if(under == 32)
		returned = 28;
	else if(under == 20)
		returned = 27;
	else if(under == 7)
		returned = 26;
	else if(under == 11)
		returned = 25;
	else if(under == 30)
		returned = 24;
	else if(under == 26)
		returned = 23;
	else if(under == 9)
		returned = 22;
	else if(under == 28)
		returned = 21;
	else if(under == 37)
		returned = 20;
	else if(under == 2)
		returned = 19;
	else if(under == 14)
		returned = 18;
	else if(under == 35)
		returned = 17;
	else if(under == 23)
		returned = 16;
	else if(under == 4)
		returned = 15;
	else if(under == 16)
		returned = 14;
	else if(under == 33)
		returned = 13;
	else if(under == 21)
		returned = 12;
	else if(under == 6)
		returned = 11;
	else if(under == 18)
		returned = 10;
	else if(under == 31)
		returned = 9;
	else if(under == 19)
		returned = 8;
	else if(under == 8)
		returned = 7;
	else if(under == 12)
		returned = 6;
	else if(under == 29)
		returned = 5;
	else if(under == 25)
		returned = 4;
	else if(under == 10)
		returned = 3;
	else if(under == 27)
		returned = 2;
	
	return returned;
}

function playAnimation(onWath) {
	var moves = calculateZero();
	moves = moves + 38;
	
	var howMuch;
	
	if(onWath == 0)
		howMuch = 0;
	else if(onWath == 1)
		howMuch = 1;
	else if(onWath == 13)
		howMuch = 2;
	else if(onWath == 36)
		howMuch = 3;
	else if(onWath == 24)
		howMuch = 4;
	else if(onWath == 3)
		howMuch = 5;
	else if(onWath == 15)
		howMuch = 6;
	else if(onWath == 34)
		howMuch = 7;
	else if(onWath == 22)
		howMuch = 8;
	else if(onWath == 5)
		howMuch = 9;
	else if(onWath == 17)
		howMuch = 10;
	else if(onWath == 32)
		howMuch = 11;
	else if(onWath == 20)
		howMuch = 12;
	else if(onWath == 7)
		howMuch = 13;
	else if(onWath == 11)
		howMuch = 14;
	else if(onWath == 30)
		howMuch = 15;
	else if(onWath == 26)
		howMuch = 16;
	else if(onWath == 9)
		howMuch = 17;
	else if(onWath == 28)
		howMuch = 18;
	else if(onWath == 37)
		howMuch = 19;
	else if(onWath == 2)
		howMuch = 20;
	else if(onWath == 14)
		howMuch = 21;
	else if(onWath == 35)
		howMuch = 22;
	else if(onWath == 23)
		howMuch = 23;
	else if(onWath == 4)
		howMuch = 24;
	else if(onWath == 16)
		howMuch = 25;
	else if(onWath == 33)
		howMuch = 26;
	else if(onWath == 21)
		howMuch = 27;
	else if(onWath == 6)
		howMuch = 28;
	else if(onWath == 18)
		howMuch = 29;
	else if(onWath == 31)
		howMuch = 30;
	else if(onWath == 19)
		howMuch = 31;
	else if(onWath == 8)
		howMuch = 32;
	else if(onWath == 12)
		howMuch = 33;
	else if(onWath == 29)
		howMuch = 34;
	else if(onWath == 25)
		howMuch = 35;
	else if(onWath == 10)
		howMuch = 36;
	else if(onWath == 27)
		howMuch = 37;
	
	moves = moves + howMuch;
	
	i = 0;
	roll(moves);
}