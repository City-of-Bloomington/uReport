<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticket
 */
$rows = '';
$person = $this->ticket->getReportedByPerson();

if ($person) {
    $name   = self::escape($person->getFullname());
    $method = self::escape($this->ticket->getResponseMethod());
    $rows.= "
    <tr><td><a href=\"{$person->getURL()}\">$name</a></td>
        <td>$method</td>
    </tr>
    ";
}
if ($rows) {
	echo "
	<div class=\"responseReminder\">
		<h2>{$this->_('contact_list')}</h2>
		<p>{$this->_('response_reminder', 'messages')}</p>
		<table>
			$rows
		</table>
	</div>
	";
}
