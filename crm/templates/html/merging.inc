<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE,0,2)); ?>">
<?php
	$this->layout = '50-50';
	$this->_include('partials/header.inc');
?>
<body>
	<?php
		$this->_include('partials/banner.inc');
		$this->_include('partials/menubar.inc');
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
	<?php $this->_include('partials/footer.inc'); ?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
