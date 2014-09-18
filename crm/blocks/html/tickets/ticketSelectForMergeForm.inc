<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$this->template->addToAsset('scripts',BASE_URL.'/js/merge.js');
?>
<form method="get" action="<?php echo BASE_URI; ?>/tickets/merge" class="ticketSelectForMergeForm">
	<fieldset><legend><?php echo $this->translate('labels.merge_tickets'); ?></legend>
		<p><?php echo $this->translate('messages.merge_tickets_description'); ?></p>
		<div>
			<input name="ticket_id_a" id="ticket_id_a" ondragover="MERGE.drag(event);" ondrop="MERGE.drop(event);" />
			<input name="ticket_id_b" id="ticket_id_b" ondragover="MERGE.drag(event);" ondrop="MERGE.drop(event);" />
			<button type="submit"><span class="fa fa-check"></span> <?php echo $this->translate('labels.merge'); ?></button>
		</div>
	</fieldset>
</form>
