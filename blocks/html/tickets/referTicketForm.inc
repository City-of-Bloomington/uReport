<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>, W. Sibo <sibow@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$referredPerson = $this->ticket->getReferredPerson() ? $this->ticket->getReferredPerson() : new Person();
?>
<div class="assignTicketForm">
	<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
		<fieldset><legend>Refer Ticket</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<table>
				<tr><td><label for="department_id">Department</label></td>
					<td><select name="department_id" id="department_id">
							<option></option>
							<?php
								$list = new DepartmentList();
								$list->find();
								foreach ($list as $department) {
									$name = View::escape($department->getName());
									echo "<option value=\"{$department->getId()}\">$name</option>";
								}
							?>
						</select>
					</td>
				</tr>
				<tr><td><label for="referredPerson_id">person_id</label></td>
					<td><input name="referredPerson_id" id="referredPerson_id"
							value="<?php echo $referredPerson->getId(); ?>" />
					</td>
				</tr>
			</table>
			<div>
				<textarea name="notes" id="notes" rows="3" cols="50"></textarea>
			</div>
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='{$this->ticket->getURL()}';"
				);
			?>
		</fieldset>
	</form>
</div>
