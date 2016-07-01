<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Substatus $this->substatus
 */
$title = $this->substatus->getId() ? $this->_('edit') : $this->_('add');
$name        = self::escape($this->substatus->getName());
$description = self::escape($this->substatus->getDescription());
?>
<section>
<header><h1><?= $title; ?></h1></header>
<form method="post" action="<?= BASE_URL; ?>/substatus/update">
	<fieldset><legend><?= $this->_('substatus'); ?></legend>
		<input name="substatus_id" type="hidden" value="<?= "{$this->substatus->getId()}"; ?>" />
		<?php
            $helper = $this->template->getHelper('field');
            echo $helper->field([
                'name'     => 'status',
                'id'       => 'status',
                'label'    => $this->_('status'),
                'required' => true,
                'value'    => $this->substatus->getStatus(),
                'options'  => [
                    ['value'=>'open',   'label'=>$this->_('open'  )],
                    ['value'=>'closed', 'label'=>$this->_('closed')]
                ]
            ]);
            echo $helper->field(['name'=>'name', 'id'=>'name', 'label'=>$this->_('name'), 'value'=>$name, 'required'=>true]);
            echo $helper->field(['name'=>'description', 'id'=>'description', 'label'=>$this->_('description'), 'value'=>$description, 'required'=>true]);
            echo $helper->field([
                'name'    => 'default',
                'label'   => $this->_('default'),
                'type'    => 'checkbox',
                'value'   => [$this->substatus->isDefault() ? 1 : 0],
                'options' => [['value'=>1, 'label'=>$this->_('default')]]
            ]);
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI.'/substatus');
		?>
	</fieldset>
</form>
</section>