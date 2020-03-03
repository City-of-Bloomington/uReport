<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        $this->_include('partials/stylesheets.inc');
        $this->addToAsset('scripts', BASE_URI.'/js/menus-'.VERSION.'.js');
    ?>
    <script type="text/javascript" src="<?= BASE_URI; ?>/js/crm.js"></script>
    <script type="text/javascript">
        CRM.BASE_URL = '<?= BASE_URL; ?>';
	</script>
    <title><?= APPLICATION_NAME; ?>: <?= self::escape($this->title); ?></title>
</head>
