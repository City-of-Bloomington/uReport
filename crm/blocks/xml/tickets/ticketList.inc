<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 *
 * @param string $this->title
 * @param bool $this->disableButtons
 * @param Ticket $this->filterTicket A ticket to leave out of the display
 * @param string $this->moreLink
 */
use Application\Models\Person;
use Blossom\Classes\View;

echo "<tickets>";
foreach ($this->ticketList as $ticket) {
	if ($this->filterTicket && $this->filterTicket->getId()==$ticket->getId()) {
		continue;
	}
	$status      = self::escape($ticket->getStatus());
	$location    = self::escape($ticket->getLocation());
	$enteredDate = $ticket->getEnteredDate(DATE_FORMAT);
	$enteredBy = $ticket->getEnteredByPerson_id()
		? self::escape($ticket->getEnteredByPerson()->getFullname())
		: '';
	$category = $ticket->getCategory_id()
		? self::escape($ticket->getCategory()->getName())
		: '';
	echo "
	<ticket>
		<status>$status</status>
		<location>$location</location>
		<enteredDate>$enteredDate</enteredDate>
	";
		if (Person::isAllowed('People')) {
			echo "<enteredBy>$enteredBy</enteredBy>";
		}
	echo "
		<category>$category</category>
	</ticket>
	";
}
echo "</tickets>";
