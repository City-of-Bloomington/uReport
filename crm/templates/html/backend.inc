<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE,0,2)); ?>">
<?php
	$this->layout = 'twoColumn_200-a';
	$dir = dirname(__FILE__);
	include $dir.'/partials/header.inc';
?>
<body>
	<?php
		include $dir.'/partials/banner.inc';
		include $dir.'/partials/menubar.inc';
	?>

	<div id="panel-container">
		<div id="sidebar" class="panel">
			<?php
				include $dir.'/partials/sidebar.inc';
			?>
		</div>
		<div id="content" class="panel">
			<?php
				include BLOSSOM.'/errorMessages.php';
				echo $this->includeBlocks();
			?>
		</div>
	</div>
	<?php include $dir.'/partials/footer.inc'; ?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
