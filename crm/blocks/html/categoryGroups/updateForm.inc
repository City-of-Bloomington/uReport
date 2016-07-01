<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param CategoryGroup $this->categoryGroup
 */

$title = $this->categoryGroup->getId()
	? $this->_('edit').' '.$this->_('categoryGroup')
	: $this->_('add' ).' '.$this->_('categoryGroup');
$name = self::escape($this->categoryGroup->getName());
?>
<section class="updateCategoryGroupForm">
    <header><h1><?= $title; ?></h1></header>
	<form method="post" action="<?= BASE_URI; ?>/categoryGroups/update">
		<fieldset>
			<input name="categoryGroup_id" type="hidden" value="<?= $this->categoryGroup->getId(); ?>" />
			<?php
                $helper = $this->template->getHelper('field');
                echo $helper->field(['name'=>'name', 'id'=>'name', 'label'=>$this->_('name'), 'value'=>$name, 'required'=>true]);

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/categoryGroups');
			?>
		</fieldset>
	</form>
</section>
