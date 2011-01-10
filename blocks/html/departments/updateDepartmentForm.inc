<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department this->department
 */
$title = $this->department->getId() ? 'Edit' : 'Add';
?>
<h1><?php echo $title; ?> Department</h1>
<form method="post" action="<?php echo BASE_URL.'/departments/updateDepartment.php'; ?>">
	<fieldset><legend>Department Info</legend>
		<input name="department_id" type="hidden" value="<?php echo $this->department->getId(); ?>" />
		<table>

			<tr><td><label for="name" class="required">Name</label></td>
				<td><input name="name" id="name"
						value="<?php echo View::escape($this->department->getName()); ?>" />
				</td>
			</tr>

			<tr><td><label for="default_user_id" class="required">Default User</label></td>
				<td><input name="default_user_id" id="default_user_id"
						value="<?php echo $this->department->getDefault_user_id(); ?>" />
				</td>
			</tr>


		</table>
		<?php
			echo $this->template->formButton('Submit','submit','submit');
			echo $this->template->formButton(
				'Cancel','button','cancel',null,"document.location.href='".BASE_URL."/departments';"
			);
		?>
	</fieldset>
</form>
