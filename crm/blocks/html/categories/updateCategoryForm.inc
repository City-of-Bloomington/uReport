<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 */
use Application\Models\CategoryGroupTable;
use Application\Models\DepartmentTable;
use Blossom\Classes\View;

$title = $this->category->getId()
	? $this->translate('labels.edit').' '.$this->translate(array('labels.category','labels.categories',1))
	: $this->translate('labels.add' ).' '.$this->translate(array('labels.category','labels.categories',1));

$fields = array(
	'name', 'description', 'postingPermissionLevel', 'displayPermissionLevel',
	'categoryGroup_id', 'department_id'
);
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = isset($_POST[$field]) ? View::escape($_POST[$field]) : View::escape($this->category->$get());
}
?>
<h2><?php echo $title; ?></h2>
<form method="post" action="<?php echo BASE_URI; ?>/categories/update">
	<fieldset><legend><?php echo $this->translate(array('labels.category','labels.categories',1)); ?></legend>
		<input name="category_id" type="hidden" value="<?php echo $this->category->getId(); ?>" />
		<table>
			<tr><td><label for="name" class="required">
						<?php echo $this->translate('labels.name'); ?>
						<span class="fa fa-asterisk"></span>
					</label>
				</td>
				<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
			</tr>
			<tr><td><label for="categoryGroup_id"><?php echo $this->translate(array('labels.categoryGroup','labels.categoryGroup',1)); ?></label></td>
				<td><select name="categoryGroup_id" id="categoryGroup_id">
					<?php
						$t = new CategoryGroupTable();
						$groups = $t->find();
						foreach ($groups as $group) {
							$selected = $categoryGroup_id==$group->getId()
								? 'selected="selected"'
								: '';
							$name = View::escape($group->getName());
							echo "<option value=\"{$group->getId()}\" $selected>$name</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr><td><label for="description"><?php echo $this->translate('labels.description'); ?></label></td>
				<td><textarea name="description" id="description" rows="3" cols="50"><?php echo $description; ?></textarea>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>
			<?php echo $this->translate('labels.sla'); ?>
			(<?php echo $this->translate('labels.service_level_agreement'); ?>)
		</legend>
		<p><?php echo $this->translate('text.service_level_agreement_description'); ?></p>
		<table>
			<tr><td><label for="department_id" class="required">
					<?php echo $this->translate(array('labels.department','labels.departments',1)); ?>
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
							$name = View::escape($department->getName());
							echo "<option value=\"{$department->getId()}\" $selected >$name</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr><td><label for="slaDays"><?php echo $this->translate(array('labels.day','labels.days',2)); ?></label></td>
				<td><input name="slaDays" id="slaDays" size="2" maxlength="2"
						value="<?php echo $this->category->getSlaDays(); ?>" />
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset><legend><?php echo $this->translate('labels.permissions_required'); ?></legend>
		<table>
			<tr><td><label for="postingPermissionLevel"><?php echo $this->translate('labels.postingPermissionLevel'); ?></label></td>
				<td><select name="postingPermissionLevel" id="postingPermissionLevel">
						<option value="staff" <?php if ($postingPermissionLevel=='staff') echo 'selected="selected"'; ?> >
							Staff
						</option>
						<option value="public" <?php if ($postingPermissionLevel=='public') echo 'selected="selected"'; ?> >
							Public
						</option>
						<option value="anonymous" <?php if ($postingPermissionLevel=='anonymous') echo 'selected="selected"'; ?> >
							Anonymous
						</option>
					</select>
				</td>
			</tr>
			<tr><td><label for="displayPermissionLevel"><?php echo $this->translate('labels.displayPermissionLevel'); ?></label></td>
				<td><select name="displayPermissionLevel" id="displayPermissionLevel">
						<option value="staff" <?php if ($displayPermissionLevel=='staff') echo 'selected="selected"'; ?> >
							Staff
						</option>
						<option value="public" <?php if ($displayPermissionLevel=='public') echo 'selected="selected"'; ?> >
							Public
						</option>
						<option value="anonymous" <?php if ($displayPermissionLevel=='anonymous') echo 'selected="selected"'; ?> >
							Anonymous
						</option>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset><legend><?php echo $this->translate('labels.custom_fields'); ?></legend>
		<?php
			// There may not be any custom fields for this category.
			// We do not want to display "null" in this case, just
			// leave the textarea empty.
			$custom_fields = '';
			if (isset($_POST['custom_fields'])) {
				$custom_fields = View::escape($_POST['custom_fields']);
			}
			else {
				$fields = $this->category->getCustomFields();
				if ($fields) {
					$custom_fields = json_encode($fields);
				}
			}
		?>
		<textarea name="custom_fields" id="custom_fields" rows="30" cols="50"><?php echo $custom_fields; ?></textarea>
		<div><p>Custom fields are defined in JSON.
				Each field can have a name, label, type and values.
				Each field requires at least a name.
			</p>
			<h3>Type</h3>
			<p>The currently supported types are taken from the Open311 spec.
				<a href="http://wiki.open311.org/GeoReport_v2#GET_Service_Definition">
					http://wiki.open311.org/GeoReport_v2#GET_Service_Definition
				</a>
			</p>
			<ul><li>string: A string of characters without line breaks. Represented in an HTML from using an &lt;input&gt; tag</li>
				<!--<li>number: A numeric value. Represented in an HTML from using an &lt;input&gt; tag</li> -->
				<!--<li>datetime: The input generated must be able to transform into a valid ISO 8601 date. Represented in an HTML from using &lt;input&gt; tags</li> -->
				<li>text:  A string of characters that may contain line breaks. Represented in an HTML from using an &lt;textarea&gt; tag</li>
				<li>singlevaluelist:  A set of predefined values (specified in this response) where only one value may be selected. Represented in an HTML from using the &lt;select&gt; and &lt;option&gt; tags</li>
				<li>multivaluelist:  A set of predefined values (specified in this response) where several values may be selected. Represented in an HTML from using the &lt;select multiple="multiple"&gt; and &lt;option&gt; tags</li>
			</ul>
			<p>
				If the type is not provided, or an unknown type is used, it will be treated as "string".
			</p>
			<h3>Values</h3>
			<p>Values are the list of predefined options for singlevaluelist and multivaluelist.
				Values are required if type is set to singlevaluelist or multivaluelist.
			</p>
			<h3>Example</h3>
				<code><pre>[
	{"name":"one","label":"Default"},
	{"name":"two","label":"Single Value List","type":"singlevaluelist","values":["a","b","c"]},
	{"name":"three","label":"Multi Value List","type":"multivaluelist","values":["d","e","f","g"]},
	{"name":"four","label":"Text Box","type":"text"}
]
				</pre></code>
		</div>
	</fieldset>
	<fieldset><legend><?php echo $this->translate('labels.submit'); ?></legend>
		<?php
			$helper = $this->template->getHelper('saveAndCancelButtons');
			echo $helper->saveAndCancelButtons(BASE_URI.'/categories');
		?>
	</fieldset>
</form>
