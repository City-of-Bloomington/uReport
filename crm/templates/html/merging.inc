<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html>
<?php
	$this->layout = 'merging';
	$dir = dirname(__FILE__);
	include $dir.'/partials/header.inc';
?>
<body>
	<?php
		include $dir.'/partials/banner.inc';
		include $dir.'/partials/menubar.inc';
	?>

	<div id="panel-container">
		<div id="content-panel">
			<?php
				include FRAMEWORK.'/errorMessages.php';

				echo $this->includeBlocks();
			?>
		</div>
		<div id="merge-panel-one">
			<?php echo $this->includeBlocks('merge-panel-one'); ?>
		</div>
		<div id="merge-panel-two">
			<?php echo $this->includeBlocks('merge-panel-two'); ?>
		</div>
	</div>
	<?php include $dir.'/partials/footer.inc'; ?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
