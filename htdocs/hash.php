<?php
echo "Hash :<p style=\"word-wrap: break-word\">".$_GET['hash']."</p>";

if(isset($_GET['secret']))
	echo "Secret: <p style=\"word-wrap:break-word\">".$_GET['secret']."</p>";
?>
<center><a style="text-decoration:underline;cursor:pointer" id="close" onclick="parent.$('.coinflip-game').hide();parent.$('#iframe').attr('src', '');">Close</a></center>