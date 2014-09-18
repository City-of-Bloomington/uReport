<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$this->template->addToAsset('scripts',BASE_URI.'/js/merge.js');
?>
<form method="get" action="<?php echo BASE_URI; ?>/people/merge" class="personSelectForMergeForm">
	<fieldset><legend><?php echo $this->translate('labels.merge_people'); ?></legend>
		<p><?php echo $this->translate('messages.merge_people_description'); ?></p>
		<div>
			<input name="person_id_a" id="person_id_a" ondragover="MERGE.drag(event);" ondrop="MERGE.drop(event);" />
			<input name="person_id_b" id="person_id_b" ondragover="MERGE.drag(event);" ondrop="MERGE.drop(event);" />
			<button type="submit">
				<span class="fa fa-check"></span>
				<?php echo $this->translate('labels.merge'); ?>
			</button>
		</div>
	</fieldset>
</form>
