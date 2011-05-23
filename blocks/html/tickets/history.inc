<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
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
				<th>Entered By</th>
				<th>Action</th>
				<th>Person</th>
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
				$action = View::escape($event->getAction());

				$url = new URL(BASE_URL.'/people/viewPerson.php');
				$enteredByPerson = View::escape($event->getPersonData('enteredByPerson','fullname'));
				if ($enteredByPerson) {
					$url->person_id = (string)$event->getPersonData('enteredByPerson','_id');
					$enteredByPerson = "<a href=\"$url\">$enteredByPerson</a>";
				}
				$actionPerson = View::escape($event->getPersonData('actionPerson','fullname'));
				if ($actionPerson) {
					$url->person_id = (string)$event->getPersonData('actionPerson','_id');
					$actionPerson = "<a href=\"$url\">$actionPerson</a>";
				}
				echo "
				<tr><td>{$event->getActionDate(DATE_FORMAT)}</td>
					<td>$enteredByPerson</td>
					<td>$action</td>
					<td>$actionPerson</td>
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
