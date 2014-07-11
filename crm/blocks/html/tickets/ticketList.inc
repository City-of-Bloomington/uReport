<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
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
use Blossom\Classes\Url;
use Blossom\Classes\View;
?>
<div class="ticketList">
	<h2><?php echo $this->title ? View::escape($this->title) : $this->translate(array('labels.ticket', 'labels.tickets', 2)); ?></h2>
	<table>
		<thead>
			<tr><th><?php echo $this->translate(array('labels.status', 'labels.statuses', 2)); ?></th>
				<th><?php echo $this->translate('labels.enteredDate'); ?></th>
				<th><?php echo $this->translate('labels.sla'); ?></th>
				<th><?php echo $this->translate(array('labels.category', 'labels.categories', 2)); ?></th>
				<th><?php echo $this->translate(array('labels.location', 'labels.locations', 2)); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->ticketList as $ticket) {
				if ($this->filterTicket
					&& "{$this->filterTicket->getId()}"=="{$ticket->getId()}") {
					continue;
				}
				$status      = View::escape($ticket->getStatus());
				$location    = View::escape($ticket->getLocation());
				$enteredDate = $ticket->getEnteredDate(DATE_FORMAT);
				$sla         = $ticket->getSlaPercentage();
				if ($sla) {
					$class = $sla<=100 ? 'onTrack' : 'pastDue';
					$sla = "<span class=\"$class\">$sla%</span>";
				}
				else {
					$sla = '';
				}

				$category = $ticket->getCategory_id()
					? View::escape($ticket->getCategory()->getName())
					: '';

				if (!$this->disableLinks) {
					$status      = "<a href=\"{$ticket->getURL()}\">$status</a>";
					$location    = "<a href=\"{$ticket->getURL()}\">$location</a>";
					$enteredDate = "<a href=\"{$ticket->getURL()}\">$enteredDate</a>";
					$category    = "<a href=\"{$ticket->getURL()}\">$category</a>";
				}
				echo "
				<tr><td>$status</td>
					<td>$enteredDate</td>
					<td>$sla</td>
					<td>$category</td>
					<td>$location</td>
					<td>
				";
					if (!$this->disableButtons
							&& Person::isAllowed('issues','update')
							&& $ticket->getStatus()!=='closed') {
						$url = new Url(BASE_URL.'/issues/update');
						$url->ticket_id = "{$ticket->getId()}";
						if (isset($_GET['person_id'])) {
							try {
								$person = new Person($_GET['person_id']);
								$url->person_id = "{$person->getId()}";
							}
							catch (Exception $e) {
								// Just ignore invalid people
							}
						}
						$helper = $this->template->getHelper('buttonLink');
						echo $helper->buttonLink(
							$url,
							$this->translate('labels.add_issue'),
							'add'
						);
					}
				echo "
					</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
	<?php
		if ($this->moreLink) {
			echo "
			<div>
				<a href=\"{$this->moreLink}\">
					{$this->translate('labels.more')} <span class=\"fa fa-angle-double-right\"></span>
				</a>
			</div>
			";
		}
	?>
</div>
