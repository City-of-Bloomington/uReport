<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<?php
	/**
	 * A template intended to be used when CRM gets embedded as
	 * an iframe on another site.  This template declares some
	 * postMessage goodness that the other site might find useful.
	 */
	$this->layout = 'full-width';
	$dir = dirname(__FILE__);
	include $dir.'/partials/header.inc';
?>
<body>
	<script type="text/javascript">
	function handleMessage(e) {
		if (e.data === 'height') {
			e.source.postMessage(document.body.scrollHeight,e.origin);
		}
	}
	window.addEventListener('message',handleMessage,false);
	</script>
	<div id="panel-container">
		<div id="content-panel">
			<?php
				include FRAMEWORK.'/errorMessages.php';

				echo $this->includeBlocks();
			?>
		</div>
	</div>
</body>
</html>
<?php
	include $dir.'/partials/garbageCollection.inc';
?>
