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
$ticket_resolution;
if ($this->ticket->getResolution()) {
	$ticket_resolution = $this->ticket->getResolution()->getId();
}
$assignedPerson = $this->ticket->getAssignedPerson();
$department;
if($assignedPerson){
	$department = $assignedPerson->getUser()->getDepartment()->getName();
}
if(isset($ticket_status) && $ticket_status == "closed"){
	$reason_style = "visible";
}
else {
	$reason_style="hidden";
	$ticket_resolution = "";	
}
?>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Change Ticket Status</legends>
		<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
		<div class="ticketInfo">
		<h1>Ticket #<?php echo $this->ticket->getId() ?></h1>
		<div><?php echo $location ?></div>
			<table>
				<tr><th>Date Opened</th>
					<td><?php echo $this->ticket->getEnteredDate() ?></td>
				</tr>
				<tr><th>Status</th>
					<td><select name="ticket[status]" id="ticket-status" onchange="setReasonVisibility(this)">
					<?php
						foreach($statuses as $status){
							$selected=($status == $ticket_status)? "selected=\"selected\"":"";
							echo "<option value=\"$status\" $selected>$status</option>";
						}
					?>
					</select>
					</td>
				</tr>
				<tr id="reason" style="visibility:<?php echo $reason_style ?>"><td>Reason</td>
					<td><select name="ticket[resolution_id]" id="ticket-resolution_id">
					<option></option>
					<?php
						$list = new ResolutionList();
						$list->find();
						foreach($list as $resolution){
							$selected="";
							if(isset($ticket_resolution)){
								$selected=($resolution->getId() == $ticket_resolution)? "selected=\"selected\"":"";
							}
							echo "<option value=\"{$resolution->getId()}\" $selected>{$resolution->getName()}</option>";
						}
					?>
					</select></td>
				</tr>
				<tr><th>Comments</th>
					<td><textarea name="notes" id="ticket-notes" cols="30" rows="3"></textarea></td>
				</tr>
				<tr><th>Staff Assigned</th>
					<td><?php echo $assignedPerson->getFullname(); ?></td>
				</tr>
				<tr><th>Department</th>
					<td><?php echo $department; ?></td>
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
	<script type="text/javascript">
		function setReasonVisibility(pros){                       
			var cc =  pros.options[pros.selectedIndex].text;
			var reason = document.getElementById('reason');
			if( cc == "closed"){
				  reason.style.visibility='visible';  
			 }			       		              
			  else {                               
			     reason.style.visibility="hidden";
				 document.getElementById("ticket-resolution_id").selectedIndex=0;
			 }			       		          
		}
	</script>	
</form>



