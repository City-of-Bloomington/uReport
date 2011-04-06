<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
	$this->layout = 'search';
	$dir = dirname(__FILE__);
	include $dir.'/partials/header.inc';
?>
<body>
	<?php
		include $dir.'/partials/banner.inc';
		include $dir.'/partials/menubar.inc';
	?>
<div id="panel-container">
	<div id="search-form">
				<?php echo $this->includeBlocks('search-form'); ?>
	</div>
	<div id="search-results">
		<?php
			include FRAMEWORK.'/errorMessages.php';
		?>
		
		<?php 
			echo $this->includeBlocks('search-results');
		?>
	</div>
</div>
	<?php include $dir.'/partials/footer.inc'; ?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
