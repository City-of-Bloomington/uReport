<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE,0,2)); ?>">
<?php
	$this->layout = 'threePanel';
	$this->_include('partials/header.inc');
?>
<body>
	<?php
		$this->_include('partials/banner.inc');
		$this->_include('partials/menubar.inc');
	?>
	<div id="panel-container">
		<?php include BLOSSOM.'/errorMessages.php'; ?>
		<div id="left" class="panel">
			<?= $this->includeBlocks('left'); ?>
		</div>
		<div id="right-top" class="panel">
			<?= $this->includeBlocks('right-top'); ?>
		</div>
		<div id="right-bottom" class="panel">
			<?= $this->includeBlocks('right-bottom'); ?>
		</div>
	</div>
	<?php $this->_include('partials/footer.inc'); ?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
