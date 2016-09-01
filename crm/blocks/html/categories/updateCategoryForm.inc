<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Category $this->category
 */
use Application\Models\CategoryGroupTable;
use Application\Models\DepartmentTable;
use Application\Models\SubstatusTable;

$fields = [
	'id', 'name', 'description', 'postingPermissionLevel', 'displayPermissionLevel',
	'categoryGroup_id', 'department_id', 'defaultPerson_id', 'notificationReplyEmail', 'active'
];
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = isset($_POST[$field]) ? self::escape($_POST[$field]) : self::escape($this->category->$get());
}

$helper = $this->template->getHelper('field');
?>
<section><header><h1><?= $this->template->title; ?></h1></header>
<form method="post" action="<?= BASE_URI; ?>/categories/update">
	<fieldset><legend><?= $this->_('category'); ?></legend>
		<input name="category_id" type="hidden" value="<?= $id; ?>" />
		<?php
            echo $helper->field(['name'=>'name', 'id'=>'name', 'label'=>$this->_('name'), 'value'=>$name, 'required'=>true]);

            echo $helper->field([
                'name'    => 'active',
                'id'      => 'active',
                'label'   => $this->_('status'),
                'type'    => 'select',
                'value'   => $active,
                'options' => [
                    ['value'=>1, 'label'=>$this->_('active_yes')],
                    ['value'=>0, 'label'=>$this->_('active_no' )]
                ]
            ]);

            $t = new CategoryGroupTable();
            $list = $t->find();
            $options = [];
            foreach ($list as $o) { $options[] = ['value'=>$o->getId(), 'label'=>self::escape($o->getName())]; }
            echo $helper->field([
                'name'    => 'categoryGroup_id',
                'id'      => 'categoryGroup_id',
                'label'   => $this->_('categoryGroup'),
                'type'    => 'select',
                'value'   => $categoryGroup_id,
                'options' => $options
            ]);

            echo $helper->field(['name'=>'description', 'id'=>'description', 'label'=>$this->_('description'), 'value'=>$description, 'type'=>'textarea']);
		?>
	</fieldset>
	<fieldset>
		<legend>
			<?= $this->_('sla'); ?>
			(<?= $this->_('service_level_agreement'); ?>)
		</legend>
		<p><?= $this->_('service_level_agreement_description', 'messages'); ?></p>
		<?php
			$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
			$this->template->addToAsset('scripts', BASE_URI.'/js/categories/chooseDepartment.js');

            $t = new DepartmentTable();
            $list = $t->find();
            $options = [];
            foreach ($list as $o) { $options[] = ['value'=>$o->getId(), 'label'=>self::escape($o->getName())]; }
            echo $helper->field([
                'name'    => 'department_id',
                'id'      => 'department_id',
                'label'   => $this->_('department'),
                'type'    => 'select',
                'value'   => $department_id,
                'options' => $options
            ]);

            $options = [['value'=>'']];
            $people  = $this->category->getDepartment()->getPeople();
            foreach ($people as $o) { $options[] = ['value'=>$o->getId(), 'label'=>self::escape($o->getFullname())]; }
            echo $helper->field([
                'name'    => 'defaultPerson_id',
                'id'      => 'defaultPerson_id',
                'label'   => $this->_('person'),
                'type'    => 'select',
                'value'   => $defaultPerson_id,
                'options' => $options
            ]);

            echo $helper->field([
                'name'  => 'slaDays',
                'id'    => 'slaDays',
                'label' => $this->_(['day', 'days', 2]),
                'value' => $this->category->getSlaDays(),
                'type'  => 'number'
            ]);
		?>
	</fieldset>
	<fieldset><legend><?= $this->_('permissions_required'); ?></legend>
        <?php
            global $ZEND_ACL;
            $options = [];
            foreach ($ZEND_ACL->getRoles() as $role) { $options[] = ['value'=>strtolower($role), 'label'=>"$role"]; }
            echo $helper->field([
                'name'    => 'postingPermissionLevel',
                'id'      => 'postingPermissionLevel',
                'label'   => $this->_('postingPermissionLevel'),
                'type'    => 'select',
                'value'   => $postingPermissionLevel,
                'options' => $options
            ]);
            $options = [];
            foreach ($ZEND_ACL->getRoles() as $role) { $options[] = ['value'=>strtolower($role), 'label'=>"$role"]; }
            echo $helper->field([
                'name'    => 'displayPermissionLevel',
                'id'      => 'displayPermissionLevel',
                'label'   => $this->_('displayPermissionLevel'),
                'type'    => 'select',
                'value'   => $displayPermissionLevel,
                'options' => $options
            ]);
        ?>
	</fieldset>
	<fieldset><legend><?= $this->_('usedForNotifications', 'messages'); ?></legend>
        <?php
            echo $helper->field([
                'name'  => 'notificationReplyEmail',
                'id'    => 'notificationReplyEmail',
                'label' => $this->_('notificationReplyEmail'),
                'value' => $notificationReplyEmail
            ]);

            $options = [
                ['value'=>1, 'label'=>$this->_('active_yes')],
                ['value'=>0, 'label'=>$this->_('active_no' )],
            ];
            echo $helper->field([
                'name'    => 'autoCloseIsActive',
                'label'   => $this->_('autoCloseIsActive'),
                'value'   => $this->category->getAutoCloseIsActive(),
                'type'    => 'radio',
                'options' => $options
            ]);

            $table = new SubstatusTable();
            $list = $table->find(['status'=>'closed']);
            $options = [['value'=>'', 'label'=>'']];
            foreach ($list as $o) { $options[] = ['value'=>$o->getId(), 'label'=>self::escape($o->getName())]; }
            echo $helper->field([
                'name'    => 'autoCloseSubstatus_id',
                'id'      => 'autoCloseSubstatus_id',
                'label'   => $this->_('autoCloseSubstatus_id'),
                'value'   => $this->category->getAutoCloseSubstatus_id(),
                'type'    => 'select',
                'options' => $options
            ]);
        ?>
	</fieldset>
	<fieldset><legend><?= $this->_('custom_fields'); ?></legend>
		<?php
			// There may not be any custom fields for this category.
			// We do not want to display "null" in this case, just
			// leave the textarea empty.
			$customFields = '';
			if (isset($_POST['customFields'])) {
				$customFields = self::escape($_POST['customFields']);
			}
			else {
				$fields = $this->category->getCustomFields();
				if ($fields) {
					$customFields = json_encode($fields);
				}
			}

			echo $helper->field([
                'name'  => 'customFields',
                'id'    => 'customFields',
                'label' => $this->_('custom_fields'),
                'value' => $customFields,
                'type'  => 'textarea'
			]);
		?>
		<div><p>Custom fields are defined in JSON.
				Each field can have a name, label, type and values.
				Each field requires at least a name.
			</p>
			<h3>Type</h3>
			<p>The currently supported types are taken from the Open311 spec.
				<a href="http://wiki.open311.org/GeoReport_v2/#get-service-definition">
					http://wiki.open311.org/GeoReport_v2/#get-service-definition
				</a>
			</p>
			<h3>Example</h3>
				<code><pre>[
	{"name":"one",  "label":"Default"},
	{"name":"two",  "label":"Single Value List","type":"singlevaluelist","values":["a","b","c"]},
	{"name":"three","label":"Multi Value List", "type":"multivaluelist", "values":["d","e","f","g"]},
	{"name":"four", "label":"Text Box",         "type":"text"}
]
				</pre></code>
		</div>
	</fieldset>
	<fieldset><legend><?= $this->_('submit'); ?></legend>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI.'/categories/view?category_id='.$id);
		?>
	</fieldset>
</form>
</section>
