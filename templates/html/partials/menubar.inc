<div id="menuBar">
	<ul><?php
			if (userIsAllowed('Tickets')) {
				echo "<li><a href=\"".BASE_URL."/tickets\">Requests</a></li>";
				echo "<li><a href=\"".BASE_URL."/locations\">Locations</a></li>";
			}
			if (userIsAllowed('People')) {
				echo "<li><a href=\"".BASE_URL."/people\">Constituents</a></li>";
			}
			if (userIsAllowed('Users')) {
				echo "<li><a href=\"".BASE_URL."/admin\">Admin</a></li>";
			}
			if (isset($_SESSION['USER'])) {
				$name = View::escape("{$_SESSION['USER']->getFirstname()} {$_SESSION['USER']->getLastname()}");
				echo "
				<li><a href=\"".BASE_URL."/login/logout.php\">Logout</a></li>
				<li>Signed in as $name</li>
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
</div>
