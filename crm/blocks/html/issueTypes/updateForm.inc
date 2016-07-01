<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param IssueType $this->issueType
 */
$name = self::escape($this->issueType->getName());
$title = $this->issueType->getId()
	? $this->_('edit').' '.$this->_('issueType')
	: $this->_('add' ).' '.$this->_('issueType');
?>
<section>
    <header><h1><?= $title; ?></h1></header>
	<form method="post" action="<?= BASE_URI; ?>/issueTypes/update">
		<input type="hidden" name="issueType_id" value="<?= $this->issueType->getId(); ?>" />

		<fieldset>
        <?php
            $helper = $this->template->getHelper('field');
            echo $helper->field(['name'=>'name', 'id'=>'name', 'label'=>$this->_('name'), 'value'=>$name, 'required'=>true]);

            $helper = $this->template->getHelper('saveAndCancelButtons');
            echo $helper->saveAndCancelButtons(BASE_URI.'/issueTypes');
        ?>
		</fieldset>
	</form>
</div>
