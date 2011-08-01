<div id="panel-one">
	<div>
		<h1>My Account</h1>
		<ul>
			<li><a href="<?php echo BASE_URL; ?>/admin/updateMyInfo.php">Personal Info</a></li>
			<li><a href="<?php echo BASE_URL; ?>/departments/updateMyDepartment.php">Department</a></li>
		</ul>
	</div>
	<?php
		if (userIsAllowed('Users')) {
			include APPLICATION_HOME.'/templates/html/partials/panel-widgets/Admin.inc';
		}
	?>
</div>
