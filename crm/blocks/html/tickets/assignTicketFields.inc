<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Department $this->currentDepartment
 */
// If the assigned person is in the currently chosen department, use the assigned person
// Otherwise, use the default person from the currently chosen department
$assignedPerson = $this->ticket->getAssignedPerson();
if (!$assignedPerson) {
	$assignedPerson = $_SESSION['USER'];
}
$selected_id = "{$assignedPerson->getData('department._id')}" == "{$this->currentDepartment->getId()}"
	? "{$assignedPerson->getId()}"
	: "{$this->currentDepartment->getData('defaultPerson._id')}";
?>
<table>
	<tr><td><label for="assignedPerson">Person</label></td>
		<td><select name="assignedPerson" id="assignedPerson">
			<?php
				foreach ($this->currentDepartment->getPeople() as $person) {
					$name = View::escape($person->getFullname());
					$selected = "{$person->getId()}"==$selected_id
						? 'selected="selected"'
						: '';
					echo "<option value=\"{$person->getId()}\" $selected>$name</option>";
				}
			?>
			</select>
		</td>
	</tr>
</table>
<div><label for="notes">Note to the assignee</label></div>
<div><textarea name="notes" id="notes"></textarea></div>
