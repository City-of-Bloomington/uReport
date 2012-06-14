<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
	$this->layout = 'threePanel';
	$dir = dirname(__FILE__);
	include $dir.'/partials/header.inc';
?>
<body>
	<?php
		include $dir.'/partials/banner.inc';
		include $dir.'/partials/menubar.inc';
	?>
	<div id="panel-container">
		<div id="ticketCreation" class="yui3-skin-sam">
			<?php include FRAMEWORK.'/errorMessages.php'; ?>
			<div id="left" class="panel">
				<?php echo $this->includeBlocks('left'); ?>
			</div>
			<div id="right-top" class="panel">
				<?php echo $this->includeBlocks('right-top'); ?>
			</div>
			<div id="right-bottom" class="panel">
				<?php echo $this->includeBlocks('right-bottom'); ?>
			</div>
		</div>
	</div>
	<?php include $dir.'/partials/footer.inc'; ?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
