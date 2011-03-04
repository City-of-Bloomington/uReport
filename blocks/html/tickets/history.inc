<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketHistoryList $this->ticketHistory
 */
?>
<div class="history">
	<table>
		<tbody>
		<?php
			foreach ($this->ticketHistory as $action) {
				$description = View::escape($action->getDescription());
				$notes = View::escape($action->getNotes());
				echo "
				<tr><td>{$action->getActionDate()}</td>
					<td>$description</td>
					<td>$notes</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>