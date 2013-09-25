<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts',BASE_URL.'/js/tickets/merge.js');
?>
<form method="get" action="<?php echo BASE_URI; ?>/tickets/merge" class="ticketSelectForMergeForm">
	<fieldset><legend><?php echo $this->translate('labels.merge_tickets'); ?></legend>
		<p><?php echo $this->translate('messages.merge_tickets_description'); ?></p>
		<div>
			<span class="yui3-dd-drop"><input name="ticket_id_a" id="ticket_id_a" /></span>
			<span class="yui3-dd-drop"><input name="ticket_id_b" id="ticket_id_b" /></span>
			<button type="submit"><span class="icon-ok"></span> <?php echo $this->translate('labels.merge'); ?></button>
		</div>
	</fieldset>
</form>
