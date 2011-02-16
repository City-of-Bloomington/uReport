<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
$title = $this->title ? View::escape($this->title) : 'Tickets';
echo "
<div class=\"ticketList\">
<h1>Tickets associated with this Address</h1>
";
$url = new URL(BASE_URL.'/tickets/viewTicket.php');

foreach ($this->ticketList as $ticket) {
	$date = $ticket->getDate('m-d-y');
	$person = $ticket->getPerson() ? View::escape($ticket->getPerson()->getFullname()) : '';
	$location = View::escape($ticket->getLocation());
	$neighborhood = View::escape($ticket->getNeighborhoodAssociation());
	$township = View::escape($ticket->getTownship());

	$url->ticket_id = $ticket->getId();
	echo "
	<div>
		<h2><a href=\"$url\">$date Ticket #{$ticket->getId()}</a> Assigned to: </h2>
	";
		$block = new Block('issues/issueList.inc',array('issueList'=>$ticket->getIssues()));
		echo $block->render('html',$this->template);
	echo "</div>";
}
echo "
	</tbody>
</table>
</div>
";