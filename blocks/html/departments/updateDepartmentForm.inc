<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
						<option value=""></option>
						<?php
							$defaultPerson = $this->department->getDefaultPerson();
							if ($defaultPerson) {
								$name = View::escape($defaultPerson['fullname']);
								echo "<option value=\"$defaultPerson[_id]\" selected=\"selected\">$name</option>";
							}
							foreach ($this->department->getPeople() as $person) {
								$name = View::escape($person->getFullname());
								if ($name) {
									echo "<option value=\"{$person->getId()}\">$name</option>";
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
	<fieldset class="actions">
		<legend>Actions</legend>
		<ul>
		<?php
			foreach (ActionList::getActions() as $action) {
				if ($action->getType()=='department') {
					$id = $action->getId();
					$name = View::escape($action->getName());
					$checked = $this->department->hasAction($name)
						? 'checked="checked"'
						: '';
					echo "
					<li><label>
							<input name=\"actions[$id]\" type=\"checkbox\" $checked />
							$name
						</label>
					</li>
					";
				}
			}
		?>
		</ul>
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
