<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE,0,2)); ?>">
<?php
	$this->layout = 'twoColumn_200-a';
	$this->_include('partials/header.inc');
?>
<body>
	<?php
		$this->_include('partials/banner.inc');
		$this->_include('partials/menubar.inc');
	?>

	<div id="panel-container">
		<div id="sidebar" class="panel">
			<?php
				$this->_include('partials/sidebar.inc');
			?>
		</div>
		<div id="content" class="panel">
			<?php
				include BLOSSOM.'/errorMessages.php';
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
