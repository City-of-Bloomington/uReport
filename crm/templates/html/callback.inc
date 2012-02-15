<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<script type="text/javascript">
	<?php
		$callback = preg_replace('/[^a-zA-Z_\.]/','',$_REQUEST['callback']);
		$data = addslashes($_REQUEST['data']);
		echo "self.opener.$callback('$data');";
	?>
	</script>
</head>
<body>
</body>
</html>
