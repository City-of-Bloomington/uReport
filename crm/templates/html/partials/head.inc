<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        $this->_include('partials/stylesheets.inc');
        $this->addToAsset('scripts', BASE_URI.'/js/menus.js');
    ?>
    <script type="text/javascript">
		var CRM = {
			BASE_URL: '<?= BASE_URL; ?>'
		};
	</script>
    <title><?= isset($this->title) ? self::escape($this->title) : APPLICATION_NAME; ?></title>
</head>
