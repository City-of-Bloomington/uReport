<!DOCTYPE html>
<html lang="<?= strtolower(substr(LOCALE, 0, 2)); ?>">
<head>
	<style>
		/* SLA Status */
		.onTrack { color:green; }
		.pastDue { color:red; }
	</style>
</head>
<body>
	<?= $this->includeBlocks(); ?>
</body>
</html>
