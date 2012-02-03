<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticketA
 * @param Ticket $this->ticketB
 */
?>
<div class="mergeForm">
	<form method="post" action="<?php echo BASE_URL; ?>/tickets/merge">
		<fieldset>
			<div class="target_a">
				<input name="targetTicket" id="targetTicket_a" type="radio" value="a" checked="checked" />
				<input name="ticket_id_a" type="hidden" value="<?php echo $this->ticketA->getId(); ?>" />
				<label for="targetTicket_a">Merge Into Case #<?php echo $this->ticketA->getId(); ?></label>
			</div>
			<div class="target_b">
				<label for="targetTicket_b">Merge Into Case #<?php echo $this->ticketB->getId(); ?></label>
				<input name="targetTicket" id="targetTicket_b" type="radio" value="b" />
				<input name="ticket_id_b" type="hidden" value="<?php echo $this->ticketB->getId(); ?>" />
			</div>
			<button type="submit" class="submit">Merge</button>
		</fieldset>
	</form>
</div>