<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<?php $lang = strtolower(substr(LOCALE, 0, 2)); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang; ?>" lang="<?= $lang; ?>">
<?php
	$this->_include('partials/head.inc');
?>
<body class="stackedPanels">
	<?php
		if (!isset($_REQUEST['popup'])) {
            $this->_include('partials/header.inc');
		}
	?>
	<main>
		<div id="ticket-panel" class="panel">
			<?php
                include BLOSSOM.'/errorMessages.php';
                echo $this->includeBlocks('ticket-panel');
            ?>
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
