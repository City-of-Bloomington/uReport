<?php $startTime = microtime(1); ?>
<head>
	<base href="<?php echo BASE_URL; ?>/" />
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />
	<style type="text/css">
		@import url('<?php echo BASE_URL; ?>/skins/local/reset.css');
		@import url('<?php echo YUI; ?>/cssfonts/fonts-min.css');
		@import url('<?php echo BASE_URL; ?>/skins/local/layouts/<?php echo $this->layout; ?>.css');
		@import url('<?php echo BASE_URL; ?>/skins/local/screen.css');
	</style>
	<script type="text/javascript">
		var BASE_URL = '<?php echo BASE_URL; ?>';
	</script>

	<title><?php echo APPLICATION_NAME; ?></title>
</head>
