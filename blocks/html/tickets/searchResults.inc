<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
$url = new URL(BASE_URL.'/tickets/viewTicket.php');

echo "
<table>
	<thead>
		<tr><th>Ticket #</th>
			<th>Category</th>
			<th>Location</th>
			<th>Received Date</th>
			<th>Received By</th>
		</tr>
	</thead>
	<tbody>
";
	foreach ($this->ticketList as $ticket) {
		$date = $ticket->getDate('Y-m-d');
		$person = $ticket->getPerson() ? View::escape($ticket->getPerson()->getFullname()) : '';
		$location = View::escape($ticket->getLocation());

		$url->ticket_id = $ticket->getId();
		foreach ($ticket->getIssues() as $issue) {
			echo "
			<tr><td><a href=\"$url\">{$ticket->getId()}</a></td>
				<td>
			";
					$categories = $ticket->getCategories();
					if (count($categories)) {
						echo '<ul>';
						foreach ($categories as $category) {
							$name = View::escape($category);
							echo "<li>$name</li>";
						}
						echo '</ul>';
					}
			echo "
				</td>
				<td>$location</td>
				<td>$date</td>
				<td>$person</td>
			</tr>
			";
		}
	}
echo "
	</tbody>
</table>
";
