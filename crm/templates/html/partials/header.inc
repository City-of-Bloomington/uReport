<?php
use Blossom\Classes\View;
?>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" type="text/css" href="<?php echo YUI; ?>/cssreset/cssreset-min.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo YUI; ?>/cssfonts/cssfonts-min.css" />
	<?php
		if ($this->layout) {
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".BASE_URI."/skins/local/layouts/{$this->layout}.css\" />";
		}
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URI; ?>/skins/local/screen.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URI; ?>/skins/Font-Awesome/css/font-awesome.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo BASE_URI; ?>/skins/local/print.css" media="print">
	<!--[if IE 7]>
		<link rel="stylesheet" href="<?php echo BASE_URI; ?>/skins/Font-Awesome/css/font-awesome-ie7.min.css">
	<![endif]-->
	<script type="text/javascript">
		var CRM = {
			BASE_URL: '<?php echo BASE_URL; ?>'
		};
	</script>
	<title><?php echo $this->title ? View::escape($this->title) : APPLICATION_NAME; ?></title>
</head>
