<div id="menuBar">
	<ul><?php
			if (userIsAllowed('Constituents')) {
				echo "<li><a href=\"".BASE_URL."/constituents\">Constituents</a></li>";
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
