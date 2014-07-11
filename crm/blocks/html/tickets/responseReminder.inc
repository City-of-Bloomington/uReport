<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
use Blossom\Classes\View;

$rows = '';
foreach ($this->ticket->getIssues() as $issue) {
	$person = $issue->getReportedByPerson();
	if ($person) {
		$name   = View::escape($person->getFullname());
		$method = View::escape($issue->getResponseMethod());
		$rows.= "
		<tr><td><a href=\"{$person->getURL()}\">$name</a></td>
			<td>$method</td>
		</tr>
		";
	}
}
if ($rows) {
	echo "
	<div class=\"responseReminder\">
		<h2>{$this->translate('labels.contact_list')}</h2>
		<p>{$this->translate('text.response_reminder')}</p>
		<table>
			$rows
		</table>
	</div>
	";
}
