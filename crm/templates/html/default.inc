<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<?php $lang = strtolower(substr(LOCALE, 0, 2)); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang; ?>" lang="<?= $lang; ?>">
<?php
	$this->_include('partials/header.inc');
?>
<body>
	<?php
		if (!isset($_REQUEST['popup'])) {
            $this->_include('partials/banner.inc' );
            $this->_include('partials/menubar.inc');
		}
	?>
	<div id="panel-container">
	<?php
		include BLOSSOM.'/errorMessages.php';
		echo $this->includeBlocks();
	?>
	</div>
	<?php $this->_include('partials/footer.inc'); ?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
