<html>
	<head>
		<?php include('src/head.php'); ?>
	</head>
	<body>
		<?php include('navbar.php'); ?>
		<div>
			<center>
				<h1 style="margin-bot:0">SteemCasino Privacy Policy</h1>
				<p>
					SteemCasino wants you to understand how and why we collect, use, and share information about you when you access and use SteemCasino.org, and other<br> online products and services (collectively, the "Services") or when you otherwise interact with us.<br><br>
					By accessing and using the website, you consent to the information collection, disclosure and use practices described in this Privacy Policy.<br> Please note that certain features or services referenced in this Privacy Policy may not be offered on the Service at all times.
				<p><br>
				<h4>Information We Collect Automatically</h4>
				<p>
					When you access or use our Services, we may also automatically collect information about you.<br><br>
					This includes:<br><br>
					<b>Log and Usage Data</b><br>
					We may log information when you access and use the Services. This may include your IP address, user-agent string, operating system, referral URLs,<br> device information (e.g., device IDs), pages visited, links clicked, user interactions, the requested URL, hardware settings, and search terms.<br><br>
					<b>Information from Cookies</b><br>
					We may receive information from cookies, which are pieces of data your device stores and sends back to us when we make requests. We use this information<br> to improve your experience, understand user activity, and improve the quality of our Services. For example, we store and retrieve information about your preferred settings.<br><br><br>
					<b>WE USE THE INFORMATION WE COLLECT:</b><br><br>
					To provide and improve the SteemCasino website, address your inquiries and verify the information you provide.<br><br>
					Prevent and investigate potentially prohibited or illegal activities, and/or violations of our posted user terms.<br><br>
					To contact you with administrative communications, marketing or promotional materials (on behalf of SteemCasino) and other information that may be of interest to you.<br><br>
					Compare information for accuracy and verify it with third parties (e.g Steemit.com, SteemConnect.com).<br><br>
					For the purposes disclosed at the time you provide your information, with your consent, and as further described in this Privacy Policy.<br><br>
					<b>We will not use your personal information for purposes other than those purposes we have disclosed to you, without your permission.<br> From time to time we may request your permission to allow us to share your personal information with third parties.<br> You may opt out of having your personal information shared with third parties, or from allowing us to use your personal information for any purpose<br> that is incompatible with the purposes for which we originally collected it or subsequently obtained your authorization.<br> If you choose to so limit the use of your personal information, certain features or SteemCasino Services may not be available to you.</b><br><br><br>
					<b>How We Share Information With Other Parties</b><br><br>
					Service providers under contract who help with parts of our business operations such as fraud prevention, bill collection, marketing, and technology services.<br>Our contracts dictate that these service providers only use your information in connection with the services they perform for us and not for their own benefit.<br><br>
					Companies or other entities that we plan to merge with or be acquired by, Should such a merger occur, we will require that the new merged entity follow this Privacy Policy<br> with respect to your personal information. You will receive prior notice of any merger.<br><br>
					Other third parties with your consent or direction to do so.<br><br><br>
					<b>Age Limit</b><br><br>
					SteemCasino is intended and directed at individuals between the ages of 18 and 21 depending on the country that you are a legal resident or citizen of.<br><br><br>
					<b>Community Forums/Feedback</b><br><br>
					We collect feedback from SteemCasino website users about their experiences with other SteemCasino users as well as on the website. Please note that any feedback<br> you provide via the <a href="https://discord.gg/RPHBBqM">Discord</a> is publicly viewable via the Server. You may choose, through such features or otherwise, to submit or post questions, comments,<br> or other content (collectively, “User Forum Content”). On very rare occasions, we may remove feedback pursuant to the relevant provisions of our Terms of Service,<br> including the Terms of Use.<br><br><br>
					<b>Changes to Privacy Policy</b><br><br>
					We may change this Privacy Policy from time to time. If we do, we will let you know by revising the date at the top of the policy. If we make a change to this policy that,<br> in our sole discretion, is material, we will provide you with additional notice. We encourage you to review the Privacy Policy whenever you access or use our Service<br> or otherwise interact with us to stay informed about our information practices and the ways you can help protect your privacy.<br> If you continue to use our Services after Privacy Policy changes go into effect, you consent to the revised policy.<br><br><br>
					<b>Contact - Questions or comments about SteemCasino may be directed to our <a href="https://discord.gg/RPHBBqM">Discord Server</a> or to steemcasino@gmail.com.</b>
				</p><br><button onClick="accept()">Accept</button><br><br><br>
			</center>
		</div>
		<script>
			function accept() {
				$.getJSON( "../src/privacy.php", function( data ) {
					if(data['status'] == "success")
						window.location = "index.php";
				});
			}
		</script>
		<?php include('src/footer.php'); ?>
	</body>
</html>