<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html>
<?php $lang = strtolower(substr(LOCALE, 0, 2)); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?= $lang; ?>" lang="<?= $lang; ?>">
<?php
	$this->_include('partials/head.inc');
?>
<body class="stackedPanels">
	<?php
		$this->_include('partials/header.inc');
	?>
	<main>
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
	<?php
		$this->_include('partials/footer.inc');
	?>
</body>
</html>
<?php
	$this->_include('partials/garbageCollection.inc');
?>
