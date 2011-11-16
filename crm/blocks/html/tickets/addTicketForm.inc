<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Issue $this->issue
 * @param Department $this->currentDepartment
 */
$category = $this->ticket->getCategory();
?>
<div class="addTicketForm">
	<h1>New <?php echo View::escape($category->getName()); ?> Case</h1>
	<?php include APPLICATION_HOME.'/blocks/html/departments/chooseDepartmentForm.inc'; ?>
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/addTicket.php">
		<fieldset class="ticketFields">
			<legend>Location Info</legend>
			<table>
				<tr><td><label for="ticket-location">Location</label></td>
					<td><input name="ticket[location]" id="ticket-location"
							value="<?php echo View::escape($this->ticket->getLocation()); ?>" />
					</td>
				</tr>
				<tr><td><label for="ticket-city">City</label></td>
					<td><input name="ticket[city]" id="ticket-city"
							value="<?php echo View::escape($this->ticket->getCity()); ?>" />
					</td>
				</tr>
				<tr><td><label for="ticket-state">State</label></td>
					<td><input name="ticket[state]" id="ticket-state"
							value="<?php echo View::escape($this->ticket->getState()); ?>" />
					</td>
				</tr>
				<tr><td><label for="ticket-zip">Zip</label></td>
					<td><input name="ticket[zip]" id="ticket-zip"
							value="<?php echo View::escape($this->ticket->getZip()); ?>" />
					</td>
				</tr>
				<tr><td><label for="ticket-latitude">Latitude</label></td>
					<td><input name="ticket[latitude]" id="ticket-latitude"
							value="<?php echo $this->ticket->getLatitude(); ?>" />
					</td>
				</tr>
				<tr><td><label for="ticket-longitude">Longitude</label></td>
					<td><input name="ticket[longitude]" id="ticket-longitude"
							value="<?php echo $this->ticket->getLongitude(); ?>" />
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="issueFields">
			<legend>Issue Information</legend>
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/issueFields.inc';
				include APPLICATION_HOME.'/blocks/html/tickets/issueCustomFields.inc';
			?>
		</fieldset>
		<fieldset class="assignmentFields">
			<legend>Assignment Information</legend>
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/assignTicketFields.inc';
			?>
		</fieldset>


		<fieldset>
			<input name="category_id" type="hidden" value="<?php echo $category->getId(); ?>" />
			<?php
				echo $this->template->formButton('Submit','submit','submit');
				echo $this->template->formButton(
					'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/tickets';"
				);
			?>
		</fieldset>
	</form>
</div>
<?php
	$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
	$this->template->addToAsset('scripts',BASE_URL.'/js/tickets/chooseLocation.js');
	$this->template->addToAsset('scripts',BASE_URL.'/js/tickets/choosePerson.js');
	$this->template->addToAsset('scripts',BASE_URL.'/js/tickets/chooseDepartment.js');
?>
