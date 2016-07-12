<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Ticket $this->ticketA
 * @param Ticket $this->ticketB
 */
?>
<div class="mergeForm">
	<form method="post" action="<?= BASE_URI; ?>/tickets/merge">
		<fieldset>
			<div class="target_a">
				<input name="targetTicket" id="targetTicket_a" type="radio" value="a" checked="checked" />
				<input name="ticket_id_a" type="hidden" value="<?= $this->ticketA->getId(); ?>" />
				<label for="targetTicket_a">
				<?php
					echo $this->_('merge_into_ticket').' #'.$this->ticketA->getId();
				?>
				</label>
			</div>
			<div class="target_b">
				<label for="targetTicket_b">
				<?php
					echo $this->_('merge_into_ticket').' #'.$this->ticketB->getId();
				?>
				</label>
				<input name="targetTicket" id="targetTicket_b" type="radio" value="b" />
				<input name="ticket_id_b" type="hidden" value="<?= $this->ticketB->getId(); ?>" />
			</div>
			<button type="submit" class="submit"><?= $this->_('merge'); ?></button>
		</fieldset>
	</form>
</div>
