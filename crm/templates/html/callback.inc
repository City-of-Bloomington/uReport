<!DOCTYPE html>
<html lang="<?php echo strtolower(substr(LOCALE,0,2)); ?>">
<head>
	<script type="text/javascript">
	<?php
		$callback = preg_replace('/[^a-zA-Z_\.]/','',$_REQUEST['callback']);
		$data = !empty($_REQUEST['data'])
			? "'".addslashes($_REQUEST['data'])."'"
			: '';
		echo "self.opener.$callback($data);";
	?>
	</script>
</head>
<body>
</body>
</html>
