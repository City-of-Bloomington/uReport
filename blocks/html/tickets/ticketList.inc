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
<div class="ticketList">
	<h1><?php echo $this->title ? View::escape($this->title) : 'Cases'; ?></h1>
	<table>
		<thead>
			<tr><th>Status</th>
				<th>Location</th>
				<th>Entered Date</th>
				<th>Entered By</th>
				<th>Categories</th>
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
				$status = View::escape($ticket->getStatus());
				$location = View::escape($ticket->getLocation());
				$enteredDate = $ticket->getEnteredDate(DATE_FORMAT);
				$enteredBy = View::escape($ticket->getData('enteredByPerson.fullname'));
				if (!$this->disableLinks) {
					$status = "<a href=\"{$ticket->getURL()}\">$status</a>";
					$location = "<a href=\"{$ticket->getURL()}\">$location</a>";
					$enteredDate = "<a href=\"{$ticket->getURL()}\">$enteredDate</a>";
					$enteredBy = "<a href=\"{$ticket->getURL()}\">$enteredBy</a>";
				}
				echo "
				<tr class=\"ticketResult\"><td>$status</td>
					<td>$location</td>
					<td>$enteredDate</td>
					<td>$enteredBy</td>
					<td>
				";
					$categories = $ticket->getCategories();
					if (count($categories)) {
						echo '<ul>';
						foreach ($categories as $category) {
							echo '<li>'.View::escape($category['name']).'</li>';
						}
						echo '</ul>';

					}
				echo "
					</td>
					<td>
				";
					if (!$this->disableButtons
							&& userIsAllowed('Tickets')
							&& $ticket->getStatus()!=='closed') {
						$url = new URL(BASE_URL.'/tickets/updateIssue.php');
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
						echo $this->template->linkButton(
							'Add Issue',$url,'add',null,null,'button'
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
		if ($this->limit && count($this->ticketList)>$this->limit && $this->moreLink) {
			echo "<div><a href=\"{$this->moreLink}\">More &gt;&gt;</a></div>";
		}
		if (userIsAllowed('Tickets') && !$this->disableButtons && count($this->ticketList)>1) {
			$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
			$this->template->addToAsset('scripts',BASE_URL.'/js/tickets/merge.js');
			echo "
			<form method=\"get\" action=\"".BASE_URL."/tickets/mergeTickets.php\">
				<fieldset><legend>Merge Two Cases</legend>
					<p>Add two cases by dragging one of the rows or typing in the IDs to compare cases and move data between them.</p>
					<div>
						<span class=\"yui3-dd-drop\"><input name=\"ticket_id_a\" id=\"ticket_id_a\" /></span>
						<span class=\"yui3-dd-drop\"><input name=\"ticket_id_b\" id=\"ticket_id_b\" /><span class=\"yui3-dd-drop\">
			";
							echo $this->template->formButton(
								'Submit','submit','submit'
							);
			echo "
					</div>
				</fieldset>
			</form>
			";
		}
	?>
</div>
