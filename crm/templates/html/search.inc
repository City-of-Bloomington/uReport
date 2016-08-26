<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<?php $lang = strtolower(substr(LOCALE, 0, 2)); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang; ?>" lang="<?= $lang; ?>">
<?php
	$this->_include('partials/head.inc');
?>
<body class="searchLayout">
	<?php
		$this->_include('partials/header.inc');

		$this->addToAsset('scripts', JQUERY.'/jquery.min.js');
		$this->addToAsset('scripts', BASE_URI.'/js/slidingPanel.js');
	?>
	<main>
		<div id="panel-one" class="panel">
			<?= $this->includeBlocks('panel-one'); ?>
		</div>
		<div id="content-panel" class="panel">
			<?php
				include BLOSSOM.'/errorMessages.php';
				echo $this->includeBlocks();
			?>
		</div>
	</main>
	<?php
        $this->_include('partials/footer.inc');
		$this->_include('partials/assets.inc');
    ?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
