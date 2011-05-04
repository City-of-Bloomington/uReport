<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
?>
<table>
	<tr><td><label for="department_id">Department</label></td>
		<td><select name="department_id" id="department_id">
				<option></option>
				<?php
					$assignedPerson = $this->ticket->getAssignedPerson()
						? new Person($this->ticket->getAssignedPerson())
						: $_SESSION['USER'];

					$currentDepartment = $assignedPerson->getDepartment()
						? new Department($assignedPerson->getDepartment())
						: new Department($_SESSION['USER']->getDepartment());

					$list = new DepartmentList();
					$list->find();
					foreach ($list as $department) {
						$name = View::escape($department->getName());
						$selected = "{$department->getId()}"=="{$currentDepartment->getId()}"
							? 'selected="selected"'
							: '';
						echo "<option value=\"{$department->getId()}\" $selected>$name</option>";
					}
				?>
			</select>
		</td>
	</tr>
	<tr><td><label for="assignedPerson">Person</label></td>
		<td><select name="assignedPerson" id="assignedPerson">
			<?php
				$name = View::escape($assignedPerson->getFullname());
				echo "<option value=\"{$assignedPerson->getId()}\">$name</option>";

				foreach ($currentDepartment->getPeople() as $person) {
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
