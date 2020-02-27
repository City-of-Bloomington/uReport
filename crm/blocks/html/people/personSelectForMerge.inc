<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
$this->template->addToAsset('scripts', BASE_URI.'/js/merge.js');
?>
<form method="get" action="<?= BASE_URI; ?>/people/merge" class="personSelectForMergeForm">
	<fieldset><legend><?= $this->_('merge_people'); ?></legend>
		<p><?= $this->_('merge_people_description', 'messages'); ?></p>
		<div>
			<input name="person_id_a" id="person_id_a" ondragover="MERGE.drag(event);" ondrop="MERGE.drop(event);" />
			<input name="person_id_b" id="person_id_b" ondragover="MERGE.drag(event);" ondrop="MERGE.drop(event);" />
			<button type="submit" class="submit"><?= $this->_('merge'); ?></button>
		</div>
	</fieldset>
</form>
