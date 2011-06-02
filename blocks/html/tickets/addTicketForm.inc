<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Issue $this->issue
 * @param Department $this->currentDepartment
 */
?>
<div class="addTicketForm">
	<h1>New Case</h1>
	<?php include APPLICATION_HOME.'/blocks/html/departments/chooseDepartmentForm.inc'; ?>
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/addTicket.php">
		<fieldset class="ticketFields">
			<legend>Case Info</legend>
			<table>
				<tr><td><label for="ticket-location">location</label></td>
					<td><input name="ticket[location]" id="ticket-location"
							value="<?php echo View::escape($this->ticket->getLocation()); ?>" />
					</td>
				</tr>
				<tr><td><label for="ticket-latitude">latitude</label></td>
					<td><input name="ticket[latitude]" id="ticket-latitude"
							value="<?php echo $this->ticket->getLatitude(); ?>" />
					</td>
				</tr>
				<tr><td><label for="ticket-longitude">longitude</label></td>
					<td><input name="ticket[longitude]" id="ticket-longitude"
							value="<?php echo $this->ticket->getLongitude(); ?>" />
					</td>
				</tr>
				<tr><td><label for="ticket-address_id">address_id</label></td>
					<td><input name="ticket[address_id]" id="ticket-address_id"
							value="<?php echo $this->ticket->getAddress_id(); ?>" />
					</td>
				</tr>
				<tr><td><label for="ticket-city">city</label></td>
					<td><input name="ticket[city]" id="ticket-city"
							value="<?php echo View::escape($this->ticket->getCity()); ?>" />
					</td>
				</tr>
				<tr><td><label for="ticket-state">state</label></td>
					<td><input name="ticket[state]" id="ticket-state"
							value="<?php echo View::escape($this->ticket->getState()); ?>" />
					</td>
				</tr>
				<tr><td><label for="ticket-zip">zip</label></td>
					<td><input name="ticket[zip]" id="ticket-zip"
							value="<?php echo View::escape($this->ticket->getZip()); ?>" />
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="issueFields">
			<legend>Issue Information</legend>
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/issueFields.inc';
			?>
		</fieldset>
		<fieldset class="assignmentFields">
			<legend>Assignment Information</legend>
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/assignTicketFields.inc';
			?>
		</fieldset>


		<fieldset>
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/tickets';"
				);
			?>
		</fieldset>
	</form>
</div>