<?php
use Blossom\Classes\View;
?>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<link rel="stylesheet" type="text/css" href="<?= BASE_URI; ?>/skins/local/reset.css" />
	<?php
		if ($this->layout) {
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".BASE_URI."/skins/local/layouts/{$this->layout}.css\" />";
		}
	?>
	<link rel="stylesheet" type="text/css" href="<?= BASE_URI; ?>/skins/local/screen.css" />
	<link rel="stylesheet" type="text/css" href="<?= BASE_URI; ?>/skins/Font-Awesome/css/font-awesome.css" />
	<link rel="stylesheet" type="text/css" href="<?= BASE_URI; ?>/skins/local/print.css" media="print">
    <link rel="stylesheet" type="text/css" href="<?= JQUERY_UI; ?>/jquery-ui.css" />
    <script type="text/javascript">
		var CRM = {
			BASE_URL: '<?= BASE_URL; ?>'
		};
	</script>
	<title><?= $this->title ? self::escape($this->title) : APPLICATION_NAME; ?></title>
</head>
