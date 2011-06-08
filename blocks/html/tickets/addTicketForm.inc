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
			<legend>Location Info</legend>
			<table>
				<tr><td><label for="ticket-location">Location</label></td>
					<td><input name="ticket[location]" id="ticket-location"
							value="<?php echo View::escape($this->ticket->getLocation()); ?>" />
						<?php
							$return_url = new URL(BASE_URL."/tickets/addTicket.php?$_SERVER[QUERY_STRING]");
							if (isset($return_url->parameters['location'])) {
								unset($return_url->parameters['location']);
							}
							$url = BASE_URL."/locations?return_url=$return_url";
							echo $this->template->linkButton(
								'Find an Address',$url,'change','findAddressButton',null,'button'
							);
						?>
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
	<div id="find_location_overlay"></div>
</div>
<?php
	#$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
	#$this->template->addToAsset('scripts',BASE_URL.'/js/CRM.js');
	#$this->template->addToAsset('scripts',BASE_URL.'/js/tickets/add.js');
?>
