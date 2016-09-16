<?php
/**
 * @copyright 2006-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 */
use Application\Models\DepartmentTable;
use Application\Models\Person;

$fields = ['id', 'username', 'authenticationMethod', 'role', 'department_id'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->person->$get());
}
$this->template->addToAsset('scripts', BASE_URI.'/js/users/updateUserForm.js');
?>
<section class="updateUserForm">
    <header>
        <h1><?= $this->template->title; ?></h1>
    </header>
	<form method="post" action="<?= BASE_URL; ?>/users/update">
		<fieldset><legend><?= $this->_('login'); ?></legend>
            <?php
                $field = $this->template->getHelper('field');

                $options = [['value'=>'']];
                $table = new DepartmentTable();
                $list  = $table->find();
                foreach ($list as $d) { $options[] = ['value'=>$d->getId(), 'label'=>self::escape($d->getName())]; }
                echo $field->field([
                    'name'    => 'department_id',
                    'id'      => 'department_id',
                    'label'   => $this->_('department'),
                    'value'   => $department_id,
                    'type'    => 'select',
                    'options' => $options
                ]);

                $options = [];
                foreach (Person::getAuthenticationMethods() as $method) { $options[] = ['value'=>$method]; }
                echo $field->field([
                    'name'    => 'authenticationMethod',
                    'id'      => 'authenticationMethod',
                    'label'   => $this->_('authenticationMethod'),
                    'value'   => $authenticationMethod,
                    'type'    => 'select',
                    'options' => $options
                ]);

                echo $field->field(['name'=>'username', 'id'=>'username', 'label'=>$this->_('username'), 'value'=>$username, 'required'=>true]);
                echo $field->field(['name'=>'password', 'id'=>'password', 'label'=>$this->_('password'), 'type'=>'password']);

                $options = [['value'=>'']];
                global $ZEND_ACL;
                foreach (array_reverse($ZEND_ACL->getRoles()) as $role) { $options[] = ['value'=>$role]; }
                echo $field->field([
                    'name'    => 'role',
                    'id'      => 'role',
                    'label'   => $this->_('role'),
                    'value'   => $this->person->getRole(),
                    'type'    => 'select',
                    'options' => $options
                ]);
            ?>
			<p><?= $this->_('employee_authentication_description', 'messages'); ?></p>
		</fieldset>

		<?php $this->_include('people/personInfoFields.inc'); ?>

		<fieldset><legend><?= $this->_('submit'); ?></legend>
			<?php
				$helper = $this->template->getHelper('saveAndCancelButtons');
				echo $helper->saveAndCancelButtons(BASE_URI.'/users');
			?>
		</fieldset>
	</form>
</section>
