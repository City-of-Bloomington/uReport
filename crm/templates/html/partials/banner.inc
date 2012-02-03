<div id="banner">
	<ul id="utilityBar">
		<?php
			if (userIsAllowed('users')) {
				echo "<li><a href=\"".BASE_URL."/admin\">Admin</a></li>";
			}
			if (isset($_SESSION['USER'])) {
				$name = View::escape("{$_SESSION['USER']->getFirstname()} {$_SESSION['USER']->getLastname()}");
				echo "
				<li><a href=\"".BASE_URL."/account\">My Account</a></li>
				<li><a href=\"".BASE_URL."/login/logout\">Logout</a></li>
				<li>Signed in as $name</li>
				<li><a href=\"http://code.google.com/p/civic-crm/wiki/Home\">Help</a></li>
				";
			}
			else {
				echo "
				<li><a href=\"".BASE_URL."/login?return_url=$_SERVER[REQUEST_URI]\">
						Login
					</a>
				</li>
				";
			}
		?>
	</ul>
	<div id="application_name">
		<a href="<?php echo BASE_URL; ?>/"><?php echo APPLICATION_NAME; ?></a>
	</div>
	<div id="location_name"><?php echo LOCATION_NAME; ?></div>
</div>
