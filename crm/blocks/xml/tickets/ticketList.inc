<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 *
 * @param string $this->title
 * @param bool $this->disableButtons
 * @param Ticket $this->filterTicket A ticket to leave out of the display
 * @param int $this->limit
 * @param string $this->moreLink
 */
if ($this->limit) {
	$this->ticketList->limit($this->limit);
}
?>
<tickets>
	<?php
		foreach ($this->ticketList as $ticket) {
			echo "<ticket>";
				if ($this->filterTicket
					&& "{$this->filterTicket->getId()}"=="{$ticket->getId()}") {
					continue;
				}
				$status = View::escape($ticket->getStatus());
				$location = View::escape($ticket->getLocation());
				$enteredDate = $ticket->getEnteredDate(DATE_FORMAT);
				$enteredByPerson = $ticket->getEnteredByPerson();
				$enteredBy = $enteredByPerson ? View::escape($enteredByPerson->getFullname()) : '';
				$category = View::escape($ticket->getData('category.name'));
				echo "
				<status>$status</status>
					<location>$location</location>
					<enteredDate>$enteredDate</enteredDate>
				";
				if (userIsAllowed('People')) {
					echo "<enteredBy>$enteredBy</enteredBy>";
				}
				echo "
					<category>$category</category>
				</ticket>
				";
			}
		?>
</tickets>