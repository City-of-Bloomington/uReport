<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
$title = $this->title ? View::escape($this->title) : 'Tickets';
echo "
<h1>$title</h1>
";
$url = new URL(BASE_URL.'/tickets/viewTicket.php');

foreach ($this->ticketList as $ticket) {
	$date = $ticket->getDate('Y-m-d');
	$person = $ticket->getPerson() ? View::escape($ticket->getPerson()->getFullname()) : '';
	$location = View::escape($ticket->getLocation());
	$neighborhood = View::escape($ticket->getNeighborhoodAssociation());
	$township = View::escape($ticket->getTownship());

	$url->ticket_id = $ticket->getId();
	echo "
	<div>
		<div><a href=\"$url\">Ticket #{$ticket->getId()}</a></div>
		<table>
			<tr><td>$date</td>
				<td>Ticket created by $person</td>
			</tr>
		</table>
		<h2>Issues</h2>
		<table>
			<thead>
				<tr><th>Date</th>
					<th>Type</th>
					<th>Categories</th>
					<th>Entered By</th>
					<th>Reported By</th>
					<th>Contacted By</th>
					<th>Comments</th>
				</tr>
			</thead>
			<tbody>
	";
			foreach ($ticket->getIssues() as $issue) {
				if ($issue->getPerson()) {
					$href = BASE_URL.'/people/viewPerson.php?person_id='.$issue->getPerson_id();
					$username = View::escape($issue->getPerson()->getUsername());
					$username = "<a href=\"$href\">$username</a>";
				}
				else {
					$username = '';
				}
				if ($issue->getConstituent()) {
					$href = BASE_URL.'/people/viewPerson.php?person_id='.$issue->getConstituent_id();
					$constituent = View::escape($issue->getConstituent()->getFullname());
					$constituent = "<a href=\"$href\">$constituent</a>";
				}
				else {
					$constituent = '';
				}

				$type = View::escape($issue->getIssueType());
				$contactMethod = View::escape($issue->getContactMethod());
				$notes = View::escape($issue->getNotes());
				echo "
				<tr><td>{$issue->getDate('Y-m-d')}</td>
					<td>$type</td>
					<td>
				";
						if ($issue->hasCategories()) {
							echo '<ul>';
							foreach ($issue->getCategories() as $category) {
								$name = View::escape($category);
								echo "<li>$name</li>";
							}
							echo '</ul>';
						}
				echo "
					</td>
					<td>$username</td>
					<td>$constituent</td>
					<td>$contactMethod</td>
					<td>$notes</td>
				</tr>
				";
			}
	echo "
			</tbody>
		</table>

	</div>
	";
}
echo "
	</tbody>
</table>
";