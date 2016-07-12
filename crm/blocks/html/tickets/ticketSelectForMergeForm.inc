<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
$this->template->addToAsset('scripts', BASE_URL.'/js/merge.js');
?>
<form method="get" action="<?= BASE_URI; ?>/tickets/merge" class="ticketSelectForMergeForm">
	<fieldset><legend><?= $this->_('merge_tickets'); ?></legend>
		<p><?= $this->_('merge_tickets_description', 'messages'); ?></p>
		<div>
			<input name="ticket_id_a" id="ticket_id_a" ondragover="MERGE.drag(event);" ondrop="MERGE.drop(event);" />
			<input name="ticket_id_b" id="ticket_id_b" ondragover="MERGE.drag(event);" ondrop="MERGE.drop(event);" />
			<button type="submit" class="submit"><?= $this->_('merge'); ?></button>
		</div>
	</fieldset>
</form>
