<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
if (isset($this->assets['scripts'])) {
    foreach ($this->assets['scripts'] as $url) {
        echo "<script type=\"text/javascript\" src=\"$url\"></script>\n";
    }
}
if (isset($this->assets['stylesheets'])) {
    foreach ($this->assets['stylesheets'] as $url) {
        echo "<link rel=\"stylesheet\" href=\"$url\" />\n";
    }
}
