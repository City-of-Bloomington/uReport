<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE,0,2)); ?>">
<?php
	$this->layout = 'twoColumn_400-a';
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
	<?php
		include BLOSSOM.'/errorMessages.php';
		$panels = array('left', 'right');
		foreach ($panels as $p) {
			if (isset($this->blocks[$p])) {
				echo "<div id=\"$p\" class=\"panel\">";
				echo $this->includeBlocks($p);
				echo "</div>";
			}
		}
		echo $this->includeBlocks();
	?>
	</div>
	<?php $this->_include('partials/footer.inc'); ?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
