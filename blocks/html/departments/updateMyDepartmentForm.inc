<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>, W Sibo <sibow@bloomington.in.gov>
 * @param Department this->department
 */
$title = 'Edit';
?>
<div class="updateDepartmentForm">
<h1><?php echo $title; ?> Department</h1>
<form method="post" action="<?php echo BASE_URL.'/departments/updateMyDepartment.php'; ?>">
	<fieldset><legend>Department Info</legend>
		<input name="department_id" type="hidden" value="<?php echo $this->department->getId(); ?>" />
		<table>

			<tr><td><label>Name</label></td>
				<td><?php echo View::escape($this->department->getName()); ?>" /></td>
			</tr>

			<tr><td><label>Default Person ID</label></td>
				<td><input name="default_person_id" id="default_person_id"
						value="<?php echo $this->department->getDefault_person_id(); ?>" />
				</td>
			</tr>


		</table>
	</fieldset>
	<fieldset class="actions">
		<legend>Actions</legend>
		<ul>
		<?php
			$list = new ActionList();
			$list->find(array('type'=>'user'));
			foreach ($list as $action) {
				$name = View::escape($action->getName());
				$checked = $this->department->hasAction($action)
					? 'checked="checked"'
					: '';
				echo "
				<li><label>
						<input name=\"actions[{$action->getId()}]\" type=\"checkbox\" $checked />
						$name
					</label>
				</li>
				";
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
		<?php
			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/departments';"
			);
		?>
	</fieldset>
</form>
</div>