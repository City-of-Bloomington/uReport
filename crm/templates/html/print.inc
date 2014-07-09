<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
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
