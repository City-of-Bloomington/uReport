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
		<p>Don't forget to contact these people to let them know what's going on.
			And then, don't forget to log the response on the issue.
		</p>
		<table>
			$rows
		</table>
	</div>
	";
}