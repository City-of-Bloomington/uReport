<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts',BASE_URI.'/js/people/merge.js');
?>
<form method="get" action="<?php echo BASE_URI; ?>/people/merge" class="personSelectForMergeForm">
	<fieldset><legend>Merge Two People</legend>
		<p>Merge two people by dragging rows or typing in the person IDs.</p>
		<div>
			<span class="yui3-dd-drop"><input name="person_id_a" id="person_id_a" /></span>
			<span class="yui3-dd-drop"><input name="person_id_b" id="person_id_b" /></span>
			<button type="submit"><span class="icon-ok"></span> Merge</button>
		</div>
	</fieldset>
</form>
