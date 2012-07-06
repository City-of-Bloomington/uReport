<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
	$dir = dirname(__FILE__);
	include $dir.'/partials/header.inc';
?>
<body>
	<?php
		if (!isset($_REQUEST['popup'])) {
			include $dir.'/partials/banner.inc';
			include $dir.'/partials/menubar.inc';
		}
	?>
	<div id="panel-container">
	<?php
		include FRAMEWORK.'/errorMessages.php';
		echo $this->includeBlocks();
	?>
	</div>
	<?php include $dir.'/partials/footer.inc'; ?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
