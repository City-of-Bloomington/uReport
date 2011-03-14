<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>, W. Sibo <sibow@bloomington.in.gov>
 * @param Ticket $this->ticket
 * TODO ajax part
 */
$location = View::escape($this->ticket->getLocation());
$enteredBy = $this->ticket->getEnteredByPerson() ? $this->ticket->getEnteredByPerson()->getFullname() : '';
$status = $this->ticket->getStatus();
if ($this->ticket->getResolution()) {
	$status.= ': '.$this->ticket->getResolution();
}
$assignedPerson = $this->ticket->getAssignedPerson();
$department;
if($assignedPerson){
	$department = $assignedPerson->getUser()->getDepartment();
}
?>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Assign Ticket</legend>
		<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
		<div class=\"ticketInfo\">
			<h1>Ticket #<?php echo $this->ticket->getId(); ?></h1>
			<div>$location</div>
			<table>
				<tr><th>Date Opened</th>
					<td><?php echo $this->ticket->getEnteredDate() ?></td>
				</tr>
				<tr><th>Status</th>
					<td><?php echo $status ?></td>
				</tr>
				<tr><th>Department</th>
					<td><select name="department_id" id="ticket-department_id" onchange="getUsers()">
					<option id="">Pick Department</option>
					<?php
						$list = new DepartmentList();
						$list->find();
						foreach($list as $dept){
							$selected="";
							$name = View::escape($dept->getName());
							($dept->getId() == $department->getId()) ? $selected="selected=\"selected\"":""; 
							echo "<option value=\"{$dept->getId()} $selected\">$name</option>";
						}
					?>
					</select></td>
				</tr>
				<tr><th>Staff Assigned</th>
					<td>Staff Member</td>
					<td>Comments</td>
				</tr>
				<tr><th>&nbsp;</th><!-- use ajax to populate the list -->
					<td><select name="ticket[assignedPerson_id]" id="ticket-assignedPerson_id">
					 <?php
						if($department){
							$fields = array('department_id'=>$department->getId());
							$users = new UserList();
							$users->find($fields);
							foreach($users as $user){
$selected="";
								$name = View::escape($user->getPerson()->getFullname());
								$selected = ($user->getPerson_id() == $assignedPerson->getId()) ? "selected=\"selected\"":""; 
								echo "<option value=\"{$user->getPerson_id()}\" $selected>$name</option>";
							}
						}
					?>
					</select></td>
					<td><textarea name="notes" id="notes" rows="3" cols="30"></textarea></td>
						
				</tr>
			</table>
		</div>
	</fieldset>
	<fieldset><legend>Submit</legend>
	<?php
		echo $this->template->formButton('Submit','submit','submit');
		echo $this->template->formButton(
			'Cancel','button','cancel',null,"document.location.href='{$this->ticket->getURL()}';"
		);
	?>
	</fieldset>
</form>

