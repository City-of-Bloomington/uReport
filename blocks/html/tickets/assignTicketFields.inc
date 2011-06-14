<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Department $this->currentDepartment
 */
$assignedPerson = $this->ticket->getAssignedPerson()
	? $this->ticket->getPersonObject('assignedPerson')
	: $this->currentDepartment->getPersonObject('defaultPerson');
?>
<table>
	<tr><td><label for="assignedPerson">Person</label></td>
		<td><select name="assignedPerson" id="assignedPerson">
			<?php
				$name = View::escape($assignedPerson->getFullname());
				echo "<option value=\"{$assignedPerson->getId()}\">$name</option>";

				foreach ($this->currentDepartment->getPeople() as $person) {
					$name = View::escape($person->getFullname());
					echo "<option value=\"{$person->getId()}\">$name</option>";
				}
			?>
			</select>
		</td>
	</tr>
</table>
<div><label for="notes">Note to the assignee</label></div>
<div><textarea name="notes" id="notes" rows="3" cols="50"></textarea></div>
