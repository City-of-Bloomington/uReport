<div id="panel-one">
	<div>
		<h1>My Account</h1>
		<ul>
			<li><a href="<?php echo BASE_URL; ?>/account/update">Personal Info</a></li>
			<?php
				if ($_SESSION['USER']->getDepartment()) {
					echo "
					<li><a href=\"".BASE_URL."/account/updateMyDepartment\">
							Department
						</a>
					</li>
					";
				}
			?>
		</ul>
	</div>
	<?php
		if (userIsAllowed('users')) {
			include APPLICATION_HOME.'/templates/html/partials/panel-widgets/Admin.inc';
		}
	?>
</div>
