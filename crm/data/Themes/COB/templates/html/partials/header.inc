<header>
    <div class="container">
        <h1><a href="<?= BASE_URI; ?>"><?= self::escape(APPLICATION_NAME); ?></a></h1>
        <h2><a href="<?= BASE_URI; ?>">City of Bloomington, Indiana &ndash; John Hamilton, Mayor</a></h2>
        <div class="utilityBar">
        <?php
            $this->addToAsset('scripts', BASE_URI.'/js/menus.js');

            $this->_include('partials/panel-widgets/User_menu.inc');
            $this->_include('partials/panel-widgets/Administrator_menu.inc');
        ?>
        </div>
    </div>
    <?php $this->_include('partials/nav1.inc'); ?>
</header>
