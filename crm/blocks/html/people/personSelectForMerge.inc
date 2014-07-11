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
	<fieldset><legend><?php echo $this->translate('labels.merge_people'); ?></legend>
		<p><?php echo $this->translate('messages.merge_people_description'); ?></p>
		<div>
			<span class="yui3-dd-drop"><input name="person_id_a" id="person_id_a" /></span>
			<span class="yui3-dd-drop"><input name="person_id_b" id="person_id_b" /></span>
			<button type="submit">
				<span class="fa fa-check"></span>
				<?php echo $this->translate('labels.merge'); ?>
			</button>
		</div>
	</fieldset>
</form>
