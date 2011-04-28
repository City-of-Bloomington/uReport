<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 * @param string $this->title
 * @param bool $this->disableButtons
 * @param Ticket $this->filterTicket A ticket to leave out of the display
 */
?>
<div class="ticketList">
	<h1><?php echo $this->title ? View::escape($this->title) : 'Tickets'; ?></h1>
	<table>
		<thead>
			<tr><th>ID</th>
				<th>Status</th>
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
				$enteredBy = View::escape($ticket->getData('enteredByPerson.fullname'));
				echo "
				<tr><td><a href=\"{$ticket->getURL()}\">{$ticket->getId()}</a></td>
					<td>$status</td>
					<td>$location</td>
					<td>{$ticket->getEnteredDate(DATE_FORMAT)}</td>
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
					if (!$this->disableButtons && userIsAllowed('Issues')) {
						echo '<td>';
						$url = new URL(BASE_URL.'/issues/updateIssue.php');
						$url->ticket_id = $ticket->getId();
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
							'Add Issue to ticket',$url,'add',null,null,'button'
						);
						echo '</td>';
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
		if (!$this->disableButtons && count($this->ticketList)>1) {
			echo "
			<form method=\"get\" action=\"".BASE_URL."/tickets/mergeTickets.php\">
				<fieldset><legend>Merge Two Tickets</legend>
					<p>Add two ticket IDs to compare tickets and move data between them.</p>
					<div>
						<input name=\"ticket_id_a\" id=\"ticket_id_a\" />
						<input name=\"ticket_id_b\" id=\"ticket_id_b\" />
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
