<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE,0,2)); ?>">
<?php
	$this->layout = 'twoColumn_400-a';
	$dir = dirname(__FILE__);
	$this->_include('partials/header.inc');
?>
<body>
	<?php
		if (!isset($_REQUEST['popup'])) {
			$this->_include('partials/banner.inc');
			$this->_include('partials/menubar.inc');
		}
	?>
	<div id="panel-container">
		<?php include BLOSSOM.'/errorMessages.php'; ?>
		<div id="left" class="panel">
			<?= $this->includeBlocks('left'); ?>
		</div>
		<div id="right" class="panel">
			<?php
				echo $this->includeBlocks('right');
				echo $this->includeBlocks();
			?>
		</div>
	</div>
	<?php $this->_include('partials/footer.inc'); ?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
