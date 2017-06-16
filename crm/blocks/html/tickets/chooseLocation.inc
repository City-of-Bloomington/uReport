<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
use Blossom\Classes\Url;

$this->template->addToAsset('scripts', BASE_URI.'/js/tickets/chooseLocation.js');

$return_url = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
$url = BASE_URL."/locations?return_url=$return_url";
$location = self::escape($this->ticket->getLocation());
echo "
<header id=\"locationChooser\">
    <h1>$location</h1>
    <div class=\"tools\">
        <a id=\"findAddressButton\" class=\"map button\" href=\"$url\">
            {$this->_('find_location')}
        </a>
    </div>
</header>
";
