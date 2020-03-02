<link rel="stylesheet" type="text/css" href="<?= BASE_URI; ?>/css/screen-<?= VERSION; ?>.css" />
<?php
    if ($this->theme && is_file($this->theme.'/public/css/screen.css')) {
        $uri = BASE_URI.'/'.THEME.'/css/screen.css';
        echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$uri\" />";
    }
?>
