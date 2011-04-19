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
$name=View::escape($this->department->getName());
$defaultPerson = $this->department->getDefaultPerson();
$defaultPersonName = View::escape($defaultPerson->getFullname());
?>

<div class="updateDepartmentForm">
<h1><?php echo $title; ?> Department</h1>
<form method="post" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
	<fieldset><legend>Department Info</legend>
		<input name="department_id" type="hidden" value="<?php echo $this->department->getId(); ?>" />
		<table>

			<tr><td><label for="name" class="required">Name</label></td>
				<td><input name="name" id="name"
						value="<?php echo $name; ?>" />
				</td>
			</tr>

			<tr><td><label for="defaultPerson">Default Person</label></td>
				<td><input name="defaultPerson" id="defaultPerson"
						value="<?php echo $defaultPerson->getId(); ?>" />
				</td>
			</tr>

		</table>
	</fieldset>
	<fieldset><legend>Custom Statuses</legend>
		<p>Enter the statuses you want for the department, each separated by commas</p>
		<div>
			<input name="customStatuses" id="customStatuses"
				value="<?php echo View::escape(implode(',',$this->department->getCustomStatuses())); ?>" />
		</div>
	</fieldset>
	<fieldset class="categories">
		<legend>Categories</legend>
		<ul>
		<?php
			$list = new CategoryList();
			$list->find();
			foreach ($list as $category) {
				$name = View::escape($category);
				$checked = $this->department->hasCategory($category)
					? 'checked="checked"'
					: '';
				echo "
				<li><label>
						<input name=\"categories[{$category->getId()}]\" type=\"checkbox\" $checked />
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