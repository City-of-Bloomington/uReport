<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<?php $lang = strtolower(substr(LOCALE, 0, 2)); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang; ?>" lang="<?= $lang; ?>">
<?php
	$this->_include('partials/head.inc');
?>
<body class="merging">
	<?php
		$this->_include('partials/header.inc');
	?>
	<main>
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
	</main>
	<?php $this->_include('partials/footer.inc'); ?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
