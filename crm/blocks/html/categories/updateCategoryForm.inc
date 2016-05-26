<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Category $this->category
 */
use Application\Models\CategoryGroupTable;
use Application\Models\DepartmentTable;
use Application\Models\SubstatusTable;

$title = $this->category->getId()
	? $this->_('labels.edit').' '.$this->_(['labels.category','labels.categories',1])
	: $this->_('labels.add' ).' '.$this->_(['labels.category','labels.categories',1]);

$fields = [
	'name', 'description', 'postingPermissionLevel', 'displayPermissionLevel',
	'categoryGroup_id', 'department_id'
];
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = isset($_POST[$field]) ? self::escape($_POST[$field]) : self::escape($this->category->$get());
}
?>
<h2><?= $title; ?></h2>
<form method="post" action="<?= BASE_URI; ?>/categories/update">
	<fieldset><legend><?= $this->_(['labels.category','labels.categories',1]); ?></legend>
		<input name="category_id" type="hidden" value="<?= $this->category->getId(); ?>" />
		<table>
			<tr><td><label for="name" class="required">
						<?= $this->_('labels.name'); ?>
						<span class="fa fa-asterisk"></span>
					</label>
				</td>
				<td><input name="name" id="name" value="<?= $name; ?>" /></td>
			</tr>
			<tr><td><label for="categoryGroup_id"><?= $this->_(['labels.categoryGroup','labels.categoryGroup',1]); ?></label></td>
				<td><select name="categoryGroup_id" id="categoryGroup_id">
					<?php
						$t = new CategoryGroupTable();
						$groups = $t->find();
						foreach ($groups as $group) {
							$selected = $categoryGroup_id==$group->getId()
								? 'selected="selected"'
								: '';
							$name = self::escape($group->getName());
							echo "<option value=\"{$group->getId()}\" $selected>$name</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr><td><label for="description"><?= $this->_('labels.description'); ?></label></td>
				<td><textarea name="description" id="description" rows="3" cols="50"><?= $description; ?></textarea>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>
			<?= $this->_('labels.sla'); ?>
			(<?= $this->_('labels.service_level_agreement'); ?>)
		</legend>
		<p><?= $this->_('text.service_level_agreement_description'); ?></p>
		<table>
			<tr><td><label for="department_id" class="required">
					<?= $this->_(['labels.department','labels.departments',1]); ?>
					</label>
				</td>
				<td><select name="department_id" id="department_id">
					<?php
						$t = new DepartmentTable();
						$list = $t->find();
						foreach ($list as $department) {
							$selected = $department->getId()==$department_id
								? 'selected="selected"'
								: '';
							$name = self::escape($department->getName());
							echo "<option value=\"{$department->getId()}\" $selected >$name</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr><td><label for="slaDays"><?= $this->_(['labels.day','labels.days',2]); ?></label></td>
				<td><input name="slaDays" id="slaDays" size="2" maxlength="2"
						value="<?= $this->category->getSlaDays(); ?>" />
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset><legend><?= $this->_('labels.permissions_required'); ?></legend>
		<table>
			<tr><td><label for="postingPermissionLevel"><?= $this->_('labels.postingPermissionLevel'); ?></label></td>
				<td><select name="postingPermissionLevel" id="postingPermissionLevel">
                    <?php
                        global $ZEND_ACL;
                        foreach ($ZEND_ACL->getRoles() as $role) {
                            $label = "$role";
                            $value = strtolower($label);
                            $selected = $postingPermissionLevel===$value ? 'selected="selected"' : '';
                            echo "<option value=\"$value\" $selected>$label</option>";
                        }
                    ?>
					</select>
				</td>
			</tr>
			<tr><td><label for="displayPermissionLevel"><?php echo $this->_('labels.displayPermissionLevel'); ?></label></td>
				<td><select name="displayPermissionLevel" id="displayPermissionLevel">
                    <?php
                        foreach ($ZEND_ACL->getRoles() as $role) {
                            $label = "$role";
                            $value = strtolower($label);
                            $selected = $displayPermissionLevel===$value ? 'selected="selected"' : '';
                            echo "<option value=\"$value\" $selected>$label</option>";
                        }
                    ?>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset><legend><?= $this->_('labels.autoCloseIsActive'); ?></legend>
        <table>
            <tr><td><label><?= $this->_('labels.autoCloseIsActive'); ?></label></td>
                <td><label>
                        <input name="autoCloseIsActive" type="radio" value="1"
                            <?= $this->category->autoCloseIsActive() ? 'checked="checked"' : ''; ?> />
                        <?= $this->_('labels.active_yes'); ?>
                    </label>
                    <label>
                        <input name="autoCloseIsActive" type="radio" value="0"
                            <?= !$this->category->autoCloseIsActive() ? 'checked="checked"' : ''; ?> />
                        <?= $this->_('labels.active_no'); ?>
                    </label>
                </td>
            </tr>
            <tr><td><label   for="autoCloseSubstatus_id"><?= $this->_('labels.autoCloseSubstatus_id'); ?></label></td>
                <td><select name="autoCloseSubstatus_id" id="autoCloseSubstatus_id">
                        <option value=""></option>
                        <?php
                            $table = new SubstatusTable();
                            $list = $table->find(['status'=>'closed']);
                            foreach ($list as $s) {
                                $selected = $s->getId()===$this->category->getAutoCloseSubstatus_id()
                                    ? 'selected="selected"'
                                    : '';
                                $name = self::escape($s->getName());
                                echo "<option value=\"{$s->getId()}\" $selected>$name</option>";
                            }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
	</fieldset>
	<fieldset><legend><?= $this->_('labels.custom_fields'); ?></legend>
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
		?>
		<textarea name="customFields" id="customFields" rows="30" cols="50"><?= $customFields; ?></textarea>
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
	<fieldset><legend><?= $this->_('labels.submit'); ?></legend>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI.'/categories');
		?>
	</fieldset>
</form>
