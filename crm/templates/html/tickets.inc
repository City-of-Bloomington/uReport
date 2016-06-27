<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE,0,2)); ?>">
<?php
	$this->layout = 'stackedPanels';
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
		<?php include BLOSSOM.'/errorMessages.php'; ?>
		<div id="ticket-panel" class="panel">
			<?= $this->includeBlocks('ticket-panel'); ?>
		</div>
		<?php
			// These panels should only show up for non-popup windows
			// Also, only draw the divs if there is content for them
			$panels = ['history-panel', 'issue-panel', 'bottom-left', 'bottom-right'];
			foreach ($panels as $p) {
				if (!isset($_REQUEST['popup']) && isset($this->blocks[$p])) {
					echo "<div id=\"$p\" class=\"panel\">";
					echo $this->includeBlocks($p);
					echo "</div>";
				}
			}
		?>
	</div>
	<?php
		$this->_include('partials/footer.inc');
	?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
