<!DOCTYPE html>
<html lang="<?php echo strtolower(substr(LOCALE,0,2)); ?>">
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
		<?php include BLOSSOM.'/errorMessages.php'; ?>
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
	<?php include $dir.'/partials/footer.inc'; ?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
