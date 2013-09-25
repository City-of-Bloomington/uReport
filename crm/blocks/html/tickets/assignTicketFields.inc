<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Department $this->currentDepartment
 */
// There may not be a currentDepartment.
// This can happen when the current user is not in a department
$currentDepartment = $this->currentDepartment ? $this->currentDepartment : new Department();

// If the assigned person is in the currently chosen department, use the assigned person
// Otherwise, use the default person from the currently chosen department
$assignedPerson = $this->ticket->getAssignedPerson();
if (!$assignedPerson) {
	$assignedPerson = $_SESSION['USER'];
}

$selected_id = $assignedPerson->getDepartment_id() == $currentDepartment->getId()
	? $assignedPerson->getId()
	: $currentDepartment->getDefaultPerson_id();
?>
<table>
	<tr><td><label for="assignedPerson_id">
				<?php echo $this->translate(array('labels.person', 'labels.people', 1)); ?>
			</label>
		</td>
		<td><select name="assignedPerson_id" id="assignedPerson_id">
			<?php
				foreach ($currentDepartment->getPeople() as $person) {
					$name = View::escape($person->getFullname());
					$selected = $person->getId() == $selected_id
						? 'selected="selected"'
						: '';
					echo "<option value=\"{$person->getId()}\" $selected>$name</option>";
				}
			?>
			</select>
		</td>
	</tr>
</table>
<div><label for="notes"><?php echo $this->translate('labels.assignment_notes'); ?></label></div>
<div><textarea name="notes" id="notes"></textarea></div>
