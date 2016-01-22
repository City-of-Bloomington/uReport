<!DOCTYPE html>
<html lang="<?php echo strtolower(substr(LOCALE,0,2)); ?>">
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
	<?php
		include BLOSSOM.'/errorMessages.php';
		echo $this->includeBlocks();
	?>
	</div>
	<?php include $dir.'/partials/footer.inc'; ?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
