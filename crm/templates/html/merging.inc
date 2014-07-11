<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
	$this->layout = '50-50';
	$dir = dirname(__FILE__);
	include $dir.'/partials/header.inc';
?>
<body>
	<?php
		include $dir.'/partials/banner.inc';
		include $dir.'/partials/menubar.inc';
	?>

	<div id="panel-container">
		<?php
			include BLOSSOM.'/errorMessages.php';
			echo $this->includeBlocks();
		?>
		<div id="left">
			<?php echo $this->includeBlocks('left'); ?>
		</div>
		<div id="right">
			<?php echo $this->includeBlocks('right'); ?>
		</div>
	</div>
	<?php include $dir.'/partials/footer.inc'; ?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
