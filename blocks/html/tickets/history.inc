<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param ActionList $this->ticketHistory
 */
?>
<div class="history">
	<h1>History</h1>
	<table>
		<thead>
			<tr><th>Date</th>
				<th>Action</th>
				<th>Comments</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->ticketHistory as $action) {
				$description = View::escape($action->getEventDescription());
				$notes = View::escape($action->getNotes());

				echo "
				<tr><td>{$action->getEventDate('Y-m-d')}</td>
					<td>$description</td>
					<td>$notes</td>
				</tr>
				";

			}
		?>
		</tbody>
	</table>
</div>