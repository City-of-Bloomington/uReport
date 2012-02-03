<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>, W. Sibo <sibow@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Department $this->currentDepartment
 */
?>
<div class="assignTicketForm">
	<form method="post" action="<?php echo BASE_URI; ?>/tickets/assign">
		<fieldset><legend>Assign Case</legend>
			<input name="ticket_id" type="hidden" value="<?php echo $this->ticket->getId(); ?>" />
			<?php
				include APPLICATION_HOME.'/blocks/html/tickets/assignTicketFields.inc';
			?>
			<button type="submit" class="submit">Submit</button>
			<button type="button" class="cancel"
				onclick="document.location.href='<?php echo $this->ticket->getURL(); ?>';">
				Cancel
			</button>
		</fieldset>
	</form>
</div>
