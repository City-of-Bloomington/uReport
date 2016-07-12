<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
$onclick = isset($_REQUEST['callback'])
	? 'onclick="self.close();"'
	: '';
echo "
<button type=\"submit\" class=\"submit\">{$this->_('submit')}</button>
<a class=\"cancel btn\" href=\"{$this->ticket->getURL()}\" $onclick>{$this->_('cancel')}</a>
";
