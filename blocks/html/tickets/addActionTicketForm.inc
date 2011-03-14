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

?>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Add/Change Ticket Action</legend>
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
				<tr><th>Staff Assigned</th>
					<?php
						$name = "";
						if($this->ticket->getAssignedPerson()){
							$name = View::escape($this->ticket->getAssignedPerson()->getFullname());
						}
						echo "<td>$name</td>";
					?>
				</tr>
				</tr>
				<tr><th>Department</th>
					 <?php
						$name="";
						if($this->ticket->getAssignedPerson()){
							$dept = $this->ticket->getAssignedPerson()->getUser()->getDepartment();
							$name = View::escape($dept->getName());
						}
						echo "<td>$name</td>";
					?>
				</tr>
			</table>
		</div>
	</fieldset>
	<fieldset>
		<table>
			<tr><th>Actions</th>
				<td><select name="action_id" id="action_id">
					<option></option>
					<?php
						$actions = new ActionList(array('type'=>'user'));// prob need refine per dept
						foreach($actions as $action){
							echo "<option value=\"{$action->getId()}\">{$action->getName()}</option>";
						}
					?>
					</select></td>
				<th>Date Performed</th>
				<td><input type="text" name="actionDate" id="actionDate" size="10" maxsize="10"/></td>
				<th>Comments</th>
				<td><textarea name="notes" id="ticket-notes" rows="3" cols="40" wrap>
				</textarea></td>
			</tr>
		</table>
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

