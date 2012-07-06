<div id="banner">
	<ul id="utilityBar">
		<?php
			if (userIsAllowed('users')) {
				echo "<li><a href=\"".BASE_URI."/admin\">Admin</a></li>";
			}
			if (isset($_SESSION['USER'])) {
				$name = View::escape("{$_SESSION['USER']->getFirstname()} {$_SESSION['USER']->getLastname()}");
				echo "
				<li><a href=\"".BASE_URI."/account\">My Account</a></li>
				<li><a href=\"".BASE_URI."/login/logout\">Logout</a></li>
				<li>Signed in as $name</li>
				<li><a href=\"".BASE_URI."/help\">Help</a></li>
				";
			}
			else {
				$return_url = View::escape($_SERVER['REQUEST_URI']);
				echo "
				<li><a href=\"".BASE_URI."/login?return_url=$return_url\">
						Login
					</a>
				</li>
				";
			}
		?>
	</ul>
	<div id="application_name">
		<a href="<?php echo BASE_URI; ?>/"><?php echo APPLICATION_NAME; ?></a>
	</div>
	<div id="location_name"><?php echo LOCATION_NAME; ?></div>
</div>
