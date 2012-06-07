<div id="panel-one">
	<?php
		include APPLICATION_HOME.'/templates/html/partials/panel-widgets/MyAccount.inc';
		if (userIsAllowed('users')) {
			include APPLICATION_HOME.'/templates/html/partials/panel-widgets/Admin.inc';
		}
	?>
</div>
