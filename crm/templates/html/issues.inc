<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE,0,2)); ?>">
<?php
	$this->layout = 'stackedPanels';
	$this->_include('partials/header.inc');
?>
<body>
	<?php
		$this->_include('partials/banner.inc');
		$this->_include('partials/menubar.inc');
	?>

	<div id="panel-container">
		<div id="content-panel">
			<?php
				include BLOSSOM.'/errorMessages.php';

				$panels = [
					'ticket-panel', 'person-panel', 'issue-panel', 'bottom-left', 'bottom-right'
				];
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
	</div>
	<?php
		$this->_include('partials/footer.inc');
	?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
