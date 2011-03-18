<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Issue $this->issue
 */
?>
<div class="addTicketForm">
	<h1>New Ticket</h1>
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/addTicket.php">
		<fieldset class="ticketFields">
			<legend>Ticket Info</legend>
			<table>
				<tr><td><label for="ticket-location">location</label></td>
					<td><input name="ticket[location]" id="ticket-location"
							value="<?php echo View::escape($this->ticket->getLocation()); ?>" />
					</td>
				</tr>

				<tr><td><label for="ticket-street_address_id">street_address_id</label></td>
					<td><input name="ticket[street_address_id]" id="ticket-street_address_id" value="<?php echo $this->ticket->getStreet_address_id(); ?>" />
					</td>
				</tr>

				<tr><td><label for="ticket-subunit_id">subunit_id</label></td>
					<td><input name="ticket[subunit_id]" id="ticket-subunit_id" value="<?php echo $this->ticket->getSubunit_id(); ?>" />
					</td>
				</tr>

				<tr><td><label for="ticket-neighborhoodAssociation">neighborhoodAssociation</label></td>
					<td><input name="ticket[neighborhoodAssociation]" id="ticket-neighborhoodAssociation"
							value="<?php echo View::escape($this->ticket->getNeighborhoodAssociation()); ?>" />
					</td>
				</tr>

				<tr><td><label for="ticket-township">township</label></td>
					<td><input name="ticket[township]" id="ticket-township"
							value="<?php echo View::escape($this->ticket->getTownship()); ?>" />
					</td>
				</tr>

				<tr><td><label for="ticket-latitude">latitude</label></td>
					<td><input name="ticket[latitude]" id="ticket-latitude" value="<?php echo $this->ticket->getLatitude(); ?>" />
					</td>
				</tr>

				<tr><td><label for="ticket-longitude">longitude</label></td>
					<td><input name="ticket[longitude]" id="ticket-longitude" value="<?php echo $this->ticket->getLongitude(); ?>" />
					</td>
				</tr>

			</table>
		</fieldset>
		<fieldset class="issueFields">
			<legend>Issue Information</legend>
		<?php
			include APPLICATION_HOME.'/blocks/html/issues/issueFields.inc';
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