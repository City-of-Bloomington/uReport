<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
	$this->layout = 'twoColumn_400-a';
	$dir = dirname(__FILE__);
	include $dir.'/partials/header.inc';
?>
<body>
	<?php
		if (!isset($_REQUEST['popup'])) {
			include $dir.'/partials/banner.inc';
			include $dir.'/partials/menubar.inc';
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
	<?php include $dir.'/partials/footer.inc'; ?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
