<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param string $this->title
 */
$title    = $this->title ? self::escape($this->title) : $this->template->title;
$location = isset($_GET['location']) ? self::escape($_GET['location']) : '';
?>
<section class="findLocationForm">
    <header>
        <h1><?= $title; ?></h1>
    </header>

    <form method="get" action="<?= BASE_URL; ?>/locations">
        <fieldset>
            <input name="location" id="location" value="<?= $location; ?>" />
            <?php
                // Preserve any extra parameters passed in
                foreach ($_REQUEST as $key=>$value) {
                    if ($key != 'location') {
                        $key   = self::escape($key);
                        $value = self::escape($value);
                        echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
                    }
                }
            ?>
            <button type="submit" class="search"><?= $this->_('search'); ?></button>
            <?php
                if (isset($_REQUEST['callback'])) {
                    echo "
                    <button type=\"button\" class=\"cancel\" onclick=\"self.close();\">
                        {$this->_('cancel')}
                    </button>
                    ";
                }
                elseif (isset($_REQUEST['return_url'])) {
                    $url = self::escape($_REQUEST['return_url']);
                    echo "<a href=\"$url\" class=\"cancel button\">{$this->_('cancel')}</a>";
                }
            ?>
        </fieldset>
    </form>
</section>
