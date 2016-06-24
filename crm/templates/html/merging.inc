<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE,0,2)); ?>">
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
			<?= $this->includeBlocks('left'); ?>
		</div>
		<div id="right">
			<?= $this->includeBlocks('right'); ?>
		</div>
	</div>
	<?php include $dir.'/partials/footer.inc'; ?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
