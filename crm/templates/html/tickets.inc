<!DOCTYPE html>
<html lang="<?php echo strtolower(substr(LOCALE,0,2)); ?>">
<?php
	$this->layout = 'stackedPanels';
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
		<?php include BLOSSOM.'/errorMessages.php'; ?>
		<div id="ticket-panel" class="panel">
			<?php echo $this->includeBlocks('ticket-panel'); ?>
		</div>
		<?php
			// These panels should only show up for non-popup windows
			// Also, only draw the divs if there is content for them
			$panels = array('history-panel', 'issue-panel', 'bottom-left', 'bottom-right');
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
		include $dir.'/partials/footer.inc';
	?>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
