<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE,0,2)); ?>">
<?php
	$this->layout = 'twoColumn_300-a';
	$this->_include('partials/header.inc');
?>
<body>
	<?php
		$this->_include('partials/banner.inc');
		$this->_include('partials/menubar.inc');

		$this->addToAsset('scripts', JQUERY.'/jquery.min.js');
		$this->addToAsset('scripts', BASE_URI.'/js/slidingPanel.js');
	?>
	<div id="panel-container">
		<div id="left" class="panel">
			<?= $this->includeBlocks('left'); ?>
		</div>
		<div id="right" class="panel">
			<?php
				include BLOSSOM.'/errorMessages.php';
				echo $this->includeBlocks('right');
			?>
		</div>
	</div>
	<?php $this->_include('partials/footer.inc'); ?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
