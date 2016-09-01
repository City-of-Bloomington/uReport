<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Client $this->client
 */
use Application\Models\ContactMethodTable;

$fields = ['id', 'name', 'url', 'api_key', 'contactMethod_id', 'contactPerson_id'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f = $this->client->$get();
}
?>
<div class="updateClientForm">
	<h1><?= $this->template->title; ?></h1>
	<form method="post" action="<?= BASE_URL; ?>/clients/update">
		<fieldset>
			<legend><?= $this->_('client').' '.$this->_('info'); ?></legend>
			<input name="client_id" type="hidden" value="<?= $this->client->getId(); ?>" />
			<?php
                $helper = $this->template->getHelper('field');
                echo $helper->field(['name'=>'name',    'id'=>'name',    'label'=>$this->_('name'),    'value'=>$name,    'required'=>true]);
                echo $helper->field(['name'=>'url',     'id'=>'url',     'label'=>$this->_('url'),     'value'=>$url]);
                echo $helper->field(['name'=>'api_key', 'id'=>'api_key', 'label'=>$this->_('api_key'), 'value'=>$api_key, 'required'=>true]);

                $t = new ContactMethodTable();
                $contactMethods = $t->find();
                $options = [];
                foreach ($contactMethods as $m) {
                    $options[] = ['value'=>$m->getId(), 'label'=>self::escape($m->getName())];
                }
                echo $helper->field([
                    'name'    => 'contactMethod_id',
                    'id'      => 'contactMethod_id',
                    'label'   => $this->_('contactMethod'),
                    'value'   => $contactMethod_id,
                    'type'    => 'select',
                    'options' => $options
                ]);
			?>
		</fieldset>
		<fieldset>
			<legend><?= $this->_('contact').' '.$this->_('info'); ?></legend>
			<?php
                echo $helper->field([
                    'name'  => 'contactPerson',
                    'id'    => 'contactPerson',
                    'label' => $this->_('contactPerson'),
                    'value' => $this->client->getContactPerson(),
                    'type'  => 'person'
                ]);
			?>
		</fieldset>
		<fieldset>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/clients');
			?>
		</fieldset>
	</form>
</div>
