<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
$fields = ['firstname','middlename','lastname','organization'];
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = self::escape($this->person->$get());
}
?>
<fieldset>
	<legend><?= $this->_('person').' '.$this->_('info');?></legend>
	<input name="person_id" type="hidden" value="<?= $this->person->getId(); ?>" />
	<?php
		// Preserve any extra parameters passed in
		foreach ($_REQUEST as $key=>$value) {
			if (!in_array($key,$fields)) {
				$key   = self::escape($key);
				$value = self::escape($value);
				echo "<input type=\"hidden\" name=\"$key\" value=\"$value\" />\n";
			}
		}

		$helper = $this->template->getHelper('field');
		echo $helper->field(['name'=>'firstname',  'id'=>'firstname',  'label'=>$this->_('firstname'),  'value'=>$firstname, 'required'=>true]);
		echo $helper->field(['name'=>'middlename', 'id'=>'middlename', 'label'=>$this->_('middlename'), 'value'=>$middlename]);
		echo $helper->field(['name'=>'lastname',   'id'=>'lastname',   'label'=>$this->_('lastname'),   'value'=>$lastname,  'required'=>true]);

        // Email and Phone are only valid for new records
        if (!$this->person->getId()) {
            $email = !empty($_REQUEST['email']) ? self::escape($_REQUEST['email']) : '';
            $phone = !empty($_REQUEST['phone']) ? self::escape($_REQUEST['phone']) : '';
            echo $helper->field(['name'=>'email', 'id'=>'email', 'label'=>$this->_('email'), 'value'=>$email]);
            echo $helper->field(['name'=>'phone', 'id'=>'phone', 'label'=>$this->_('phone'), 'value'=>$phone]);
        }

		echo $helper->field(['name'=>'organization', 'id'=>'organization', 'label'=>$this->_('organization'), 'value'=>$organization]);
    ?>
</fieldset>
