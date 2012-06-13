<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
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

			<div id="ticket-panel">
				<?php echo $this->includeBlocks('ticket-panel'); ?>
			</div>
			<div id="person-panel">
				<?php echo $this->includeBlocks('person-panel'); ?>
			</div>
			<div id="issue-panel">
				<?php echo $this->includeBlocks('issue-panel'); ?>
			</div>
			<div id="location-panel">
				<?php echo $this->includeBlocks('location-panel'); ?>
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
