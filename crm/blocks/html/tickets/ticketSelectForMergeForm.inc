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
	<fieldset><legend>Merge Two Cases</legend>
		<p>Add two cases by dragging one of the rows or typing in the IDs to compare cases and move data between them.</p>
		<div>
			<span class="yui3-dd-drop"><input name="ticket_id_a" id="ticket_id_a" /></span>
			<span class="yui3-dd-drop"><input name="ticket_id_b" id="ticket_id_b" /></span>
			<button type="submit"><span class="icon-ok"></span> Merge</button>
		</div>
	</fieldset>
</form>
