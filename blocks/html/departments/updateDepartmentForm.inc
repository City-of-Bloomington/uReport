<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department this->department
 * @param string $this->return_url (optional)
 */
$return_url = $this->return_url ? $this->return_url : BASE_URL.'/departments';
$title = $this->department->getId() ? 'Edit' : 'Add';
?>
<div class="updateDepartmentForm">
<h1><?php echo $title; ?> Department</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Department Info</legend>
		<input name="department_id" type="hidden" value="<?php echo $this->department->getId(); ?>" />
		<table>
			<tr><td><label for="name" class="required">Name</label></td>
				<td><input name="name" id="name"
						value="<?php echo View::escape($this->department->getName()); ?>" />
				</td>
			</tr>

			<tr><td><label for="defaultPerson">Default Person</label></td>
				<td><select name="defaultPerson" id="defaultPerson">
					<?php
						$defaultPerson = $this->department->getDefaultPerson();
						$defaultPerson_id = '';
						if ($defaultPerson) {
							$defaultPerson_id = $defaultPerson['_id'];
						}
						foreach ($this->department->getPeople() as $person) {
							$name = View::escape($person['fullname']);
							if ($name) {
								$selected = $person['_id']==$defaultPerson_id
									? 'selected="selected"'
									: '';
								echo "<option value=\"$person[_id]\" $selected>$name</option>";
							}
						}
					?>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset><legend>Custom Statuses</legend>
		<p>Enter the statuses you want for the department, each separated by commas</p>
		<div>
		<?php
			$statuses = array();
			foreach ($this->department->getCustomStatuses() as $status) {
				$statuses[] = View::escape($status);
			}
			$statuses = implode(',',$statuses);
			echo "
			<input name=\"customStatuses\" id=\"customStatuses\"
				value=\"$statuses\" />
			";
		?>
		</div>
	</fieldset>
	<fieldset class="categories">
		<legend>Categories</legend>
		<ul>
		<?php
			$list = new CategoryList();
			$list->find();
			foreach ($list as $category) {
				$id = $category->getId();
				$name = View::escape($category->getName());
				$checked = $this->department->hasCategory($name)
					? 'checked="checked"'
					: '';
				echo "
				<li><label>
						<input name=\"categories[$id]\" type=\"checkbox\" $checked />
						$name
					</label>
				</li>
				";
			}
		?>
		</ul>
	</fieldset>
	<fieldset>
		<input name="return_url" type="hidden" value="<?php echo $return_url; ?>" />
		<?php
			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,"document.location.href='$return_url';"
			);
		?>
	</fieldset>
</form>
</div>
