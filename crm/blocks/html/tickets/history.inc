<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->history
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
			foreach ($this->history as $event) {
				$url = new URL(BASE_URL.'/people/viewPerson.php');

				$action = View::escape($event->getAction());
				if (userIsAllowed('People')) {
					$description = View::escape($event->getDescription());
					$description = $description ? $description : $action;
				}
				else {
					$description = $action;
				}
				echo "
				<tr><td>{$event->getActionDate(DATE_FORMAT)}</td>
					<td>$description</td>
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
