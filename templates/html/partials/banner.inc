<div id="banner">

	<div id="utilityBar">
	<?php
	if (userIsAllowed('Users')) {
				echo "<li><a href=\"".BASE_URL."/admin\">Admin</a></li>";
			}
			if (isset($_SESSION['USER'])) {
				$name = View::escape("{$_SESSION['USER']->getFirstname()} {$_SESSION['USER']->getLastname()}");
				echo "
				<li><a href=\"".BASE_URL."/departments/updateMyDepartment.php?return_url=$_SERVER[REQUEST_URI]\">
						My Department
					</a>
				</li>
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
	</div>
		<div id="application_name"><a href="<?php echo BASE_URL; ?>/">uReport</a></div>
	<div id="location_name">
		City of Bloomington, Mark Kruzan, Mayor
	</div>
</div>
