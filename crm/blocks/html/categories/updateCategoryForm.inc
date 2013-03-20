<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 */
$title = $this->category->getId() ? 'Edit Category' : 'Add Category';

$fields = array(
	'name', 'description', 'postingPermissionLevel', 'displayPermissionLevel',
	'categoryGroup_id', 'department_id'
);
foreach ($fields as $field) {
	$get = 'get'.ucfirst($field);
	$$field = isset($_POST[$field]) ? View::escape($_POST[$field]) : View::escape($this->category->$get());
}
?>
<h1><?php echo $title; ?></h1>
<form method="post" action="<?php echo BASE_URL; ?>/categories/update">
	<fieldset><legend>Category</legend>
		<input name="category_id" type="hidden" value="<?php echo $this->category->getId(); ?>" />
		<table>
			<tr><td><label for="name" class="required">Name</label></td>
				<td><input name="name" id="name" value="<?php echo $name; ?>" /></td>
			</tr>
			<tr><td><label for="categoryGroup_id">Group</label></td>
				<td><select name="categoryGroup_id" id="categoryGroup_id">
					<?php
						$groups = new CategoryGroupList();
						$groups->find();
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
			<tr><td><label for="description">Description</label></td>
				<td><textarea name="description" id="description" rows="3" cols="50"><?php echo $description; ?></textarea>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset><legend>SLA (Service Level Agreement)</legend>
		<p>This department agrees to handle new tickets for this category within
			the amount of time specified.
		</p>
		<table>
			<tr><td><label for="department_id">Department</label></td>
				<td><select name="department_id" id="department_id">
					<?php
						$list = new DepartmentList();
						$list->find();
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
			<tr><td><label for="slaDays">Days</label></td>
				<td><input name="slaDays" id="slaDays" size="2" maxlength="2"
						value="<?php echo $this->category->getSlaDays(); ?>" />
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset><legend>Permissions required</legend>
		<table>
			<tr><td><label for="postingPermissionLevel">Posting Cases</label></td>
				<td><select name="postingPermissionLevel" id="postingPermissionLevel">
						<option value="staff" <?php if ($postingPermissionLevel=='staff') echo 'selected="selected"'; ?> >
							Authenticated Staff
						</option>
						<option value="public" <?php if ($postingPermissionLevel=='public') echo 'selected="selected"'; ?> >
							Authenticated Public
						</option>
						<option value="anonymous" <?php if ($postingPermissionLevel=='anonymous') echo 'selected="selected"'; ?> >
							Anonymous
						</option>
					</select>
				</td>
			</tr>
			<tr><td><label for="displayPermissionLevel">Displaying Cases</label></td>
				<td><select name="displayPermissionLevel" id="displayPermissionLevel">
						<option value="staff" <?php if ($displayPermissionLevel=='staff') echo 'selected="selected"'; ?> >
							Authenticated Staff
						</option>
						<option value="public" <?php if ($displayPermissionLevel=='public') echo 'selected="selected"'; ?> >
							Authenticated Public
						</option>
						<option value="anonymous" <?php if ($displayPermissionLevel=='anonymous') echo 'selected="selected"'; ?> >
							Anonymous
						</option>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset><legend>Custom Fields</legend>
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
	<fieldset><legend>Submit</legend>
		<button type="submit" class="submit">Submit</button>
		<a class="cancel" href="<?php echo BASE_URL; ?>/categories">Cancel</a>
	</fieldset>
</form>
