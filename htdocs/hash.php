<?php
echo "Hash :<p style=\"word-wrap: break-word\">".$_GET['hash']."</p>";

if(isset($_GET['secret']))
	echo "Secret: <p style=\"word-wrap:break-word\">".$_GET['secret']."</p>";
?>