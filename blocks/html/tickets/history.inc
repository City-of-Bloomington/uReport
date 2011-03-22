<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketHistoryList $this->ticketHistory
 * @param bool $this->disableComments
 */
?>
<div class="history">
	<h1>History</h1>
	<table>
		<thead>
			<tr><th>Date</th>
				<th>Action</th>
				<?php
					if (!$this->disableComments) {
						echo '<th>Comments</th>';
					}
				?>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->ticketHistory as $event) {
				$action = View::escape($event->getDescription());
				echo "
				<tr><td>{$event->getActionDate()}</td>
					<td>$action</td>
				";
					if (!$this->disableComments) {
						$notes = View::escape($event->getNotes());
						echo "<td>$notes</td>";
					}
				echo "
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
