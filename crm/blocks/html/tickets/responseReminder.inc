<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
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
		<h2>Contact List</h2>
		<p>
			When a case is closed it is your responsibility to contact
			constituents who reported this issue to the City.  Log those
			contacts in the responses below.
		</p>
		<table>
			$rows
		</table>
	</div>
	";
}
