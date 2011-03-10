<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>, W. Sibo <sibow@bloomington.in.gov>
 * @param Ticket $this->ticket
 * TODO javascript to show change change status to closed and show
 *      closed reason
 */
$statuses = array('open','closed');
$location = View::escape($this->ticket->getLocation());
$enteredBy = $this->ticket->getEnteredByPerson() ? $this->ticket->getEnteredByPerson()->getFullname() : '';
$ticket_status = $this->ticket->getStatus();
if ($this->ticket->getResolution()) {
	$ticket_resolution = $this->ticket->getResolution();
}
$assignedPerson = $this->ticket->getAssignedPerson();
$department;
if($assignedPerson){
	$department = $asignedPerson->getDepartment()->getName();
}
?>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
	<div class=\"ticketInfo\">
	<h1>Ticket #{$this->ticket->getId()}</h1>
	<div>$location</div>
		<table>
			<tr><th>Date Opened</th>
				<td>{$this->ticket->getEnteredDate()}</td>
			</tr>
			<tr><th>Status</th>
				<td><select name="[ticket]status" id="ticket-status" onchange="showOrHideReason(this)">
				<?php
					foreach($statuses as $status){
						$selected=($status == $ticket_status)? "selected=\"selected\"":"";
						echo "<option $selected>$status</option>";
					}
				?>
				</select>
				</td>
			</tr>
			<tr id="showOption"><td>Reason</td>
				<td><select name="[ticket]resolution_id" id="ticket-resolution_id">
				<?php
					$list = $ResolutionList();
					$list->find();
					foreach($list as $resolution){
						$selected="";
						if($ticket_resolution){
							$selected=($resolution->getId() == $ticket_resolution->getId()) "selected=\"selected\"":"";
						}
						echo "<option value=\"{$resolution->getId()}\" $selected>{$resolution->getName()}</option>";
					}
				?>
				</select></td>
			</tr>
			<tr><th>Comments</th>
				<td><textarea id="ticket-notes" name="notes" cols="60" rows='3'></td>
			</tr>
		</table>
	</div>
	<?php
		echo $this->template->formButton('Submit','submit','submit');
		echo $this->template->formButton(
			'Cancel','button','cancel',null,"document.location.href='{$this->ticket->getURL()}';"
		);
	?>
	<div class="ticketInfo">
		<table>
			<tr><th>Staff Assigned</th>
				<td><?php echo $assignedPerson->getFullname(); ?></td>
			</tr>
			<tr><th>Department</th>
				<td><?php echo $department; ?></td>
			</tr>				
		</table>
	</div>
</form>

