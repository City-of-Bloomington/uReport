<!DOCTYPE html>
<html lang="<?php echo strtolower(substr(LOCALE,0,2)); ?>">
<head>
	<style>
		/* SLA Status */
		.onTrack { color:green; }
		.pastDue { color:red; }
	</style>
</head>
<body>
	<?php echo $this->includeBlocks(); ?>
</body>
</html>
