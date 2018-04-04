<div class="footer">
	<center>
		<span>
				Copyright © SteemCasino 2018. Created by <a href="credits.php">all those people</a>.<a href="#" onClick="removeFooter()" style="float:right">x</a>
		</span>
	</center>
</div>
<script>
	if(<?php if(isset($_COOKIE['footer'])) echo "1";
				else echo "0";?> == 1)
		$(".footer").hide();
	function removeFooter() {
		$(".footer").hide();
		var d = new Date();
		d.setTime(d.getTime() + 30*60*1000);
		document.cookie = "footer=close;expires=" + d.toUTCString(); 
	}
</script>