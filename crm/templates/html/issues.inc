<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
	$this->layout = 'stackedPanels';
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
			<?php include FRAMEWORK.'/errorMessages.php'; ?>

			<div id="ticket-panel" class="panel">
				<?php echo $this->includeBlocks('ticket-panel'); ?>
			</div>
			<div id="person-panel" class="panel">
				<?php echo $this->includeBlocks('person-panel'); ?>
			</div>
			<div id="issue-panel" class="panel">
				<?php echo $this->includeBlocks('issue-panel'); ?>
			</div>
			<div id="bottom-left" class="panel">
				<?php echo $this->includeBlocks('bottom-left'); ?>
			</div>
			<div id="bottom-right" class="panel">
				<?php echo $this->includeBlocks('bottom-right'); ?>
			</div>
			<?php
				echo $this->includeBlocks();
			?>
		</div>
	</div>
	<?php
		include $dir.'/partials/footer.inc';
	?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
