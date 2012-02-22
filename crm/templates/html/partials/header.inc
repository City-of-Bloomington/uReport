<head>
	<base href="<?php echo BASE_URL; ?>/" />
	<style type="text/css">
		@import url('<?php echo BASE_URL; ?>/skins/local/reset.css');
		@import url('<?php echo YUI; ?>/cssfonts/fonts-min.css');
		@import url('<?php echo BASE_URL; ?>/skins/local/layouts/<?php echo $this->layout; ?>.css');
		@import url('<?php echo BASE_URL; ?>/skins/local/screen.css');
	</style>
	<script type="text/javascript">
		var CRM = {
			BASE_URL: '<?php echo BASE_URL; ?>'
		};
	</script>
	<title><?php echo $this->title ? View::escape($this->title) : APPLICATION_NAME; ?></title>
</head>
