<?php
/**
 * @copyright 2006-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Person $this->person
 */
use Application\Models\DepartmentTable;
use Application\Models\Person;

$fields = ['id', 'username', 'role', 'department_id'];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->person->$get());
}
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
                foreach ($list['rows'] as $d) { $options[] = ['value'=>$d->getId(), 'label'=>self::escape($d->getName())]; }
                echo $field->field([
                    'name'    => 'department_id',
                    'id'      => 'department_id',
                    'label'   => $this->_('department'),
                    'value'   => $department_id,
                    'type'    => 'select',
                    'options' => $options
                ]);

                echo $field->field(['name'=>'username', 'id'=>'username', 'label'=>$this->_('username'), 'value'=>$username, 'required'=>true]);

                $options = [['value'=>'']];
                global $ACL;
                foreach (array_reverse($ACL->getRoles()) as $role) { $options[] = ['value'=>$role, 'label'=>$this->_(strtolower($role))]; }
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

        <?php
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
                $helper = $this->template->getHelper('field');
                echo $helper->field(['name'=>'firstname',  'id'=>'firstname',  'label'=>$this->_('firstname' ), 'value'=>$firstname ]);
                echo $helper->field(['name'=>'middlename', 'id'=>'middlename', 'label'=>$this->_('middlename'), 'value'=>$middlename]);
                echo $helper->field(['name'=>'lastname',   'id'=>'lastname',   'label'=>$this->_('lastname'  ), 'value'=>$lastname  ]);

                // Email and Phone are only valid for new records
                if (!$this->person->getId()) {
                    $email = !empty($_REQUEST['email']) ? self::escape($_REQUEST['email']) : '';
                    $phone = !empty($_REQUEST['phone']) ? self::escape($_REQUEST['phone']) : '';
                    echo $helper->field(['name'=>'email', 'id'=>'email', 'label'=>$this->_('email'), 'value'=>$email, 'type'=>'email']);
                    echo $helper->field(['name'=>'phone', 'id'=>'phone', 'label'=>$this->_('phone'), 'value'=>$phone, 'type'=>'tel'  ]);
                }

                echo $helper->field(['name'=>'organization', 'id'=>'organization', 'label'=>$this->_('organization'), 'value'=>$organization]);
            ?>
        </fieldset>

        <fieldset><legend><?= $this->_('submit'); ?></legend>
            <?php
                $helper = $this->template->getHelper('saveAndCancelButtons');
                echo $helper->saveAndCancelButtons(BASE_URI.'/users');
            ?>
        </fieldset>
    </form>
</section>
