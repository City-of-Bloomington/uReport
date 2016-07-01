<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Label $this->contactMethod
 */
$name = self::escape($this->contactMethod->getName());
$title = $this->contactMethod->getId()
	? $this->_('edit').' '.$this->_('contactMethod')
	: $this->_('add' ).' '.$this->_('contactMethod');
?>
<section>
    <header><h1><?= $title; ?></h1></header>
	<form method="post" action="<?= BASE_URI; ?>/contactMethods/update">
		<input type="hidden" name="contactMethod_id" value="<?= $this->contactMethod->getId(); ?>" />

		<fieldset>
            <?php
                $helper = $this->template->getHelper('field');
                echo $helper->field(['name'=>'name', 'id'=>'name', 'label'=>$this->_('name'), 'value'=>$name, 'required'=>true]);

				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/contactMethods');
			?>
		</fieldset>
	</form>
</section>
