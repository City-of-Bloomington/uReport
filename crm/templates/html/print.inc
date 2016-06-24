<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE,0,2)); ?>">
<?php
	$this->layout = 'twoColumn_300-a';
	$dir = dirname(__FILE__);
	include $dir.'/partials/header.inc';
?>
<body>
	<div id="panel-container">
		<div class="panel">
			<?php
				include BLOSSOM.'/errorMessages.php';
				echo $this->includeBlocks('right');
			?>
		</div>
	</div>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
