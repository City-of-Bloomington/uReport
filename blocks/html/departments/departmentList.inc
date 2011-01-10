<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
?>
<div class="interfaceBox">
	<h1><?php
			if (userIsAllowed('Departments')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/departments/updateDepartment.php','add'
				);
			}
		?>
		Departments
	</h1>
	<table>
		<thead>
			<tr><th></th>
				<th>Name</th>
				<th>Default User</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->departmentList as $department) {
				$editButton = '';
				if (userIsAllowed('Departments')) {
					$url = new URL(BASE_URL.'/departments/updateDepartment.php');
					$url->id = $department->getId();

					$editButton = $this->template->linkButton(
						'Edit',
						BASE_URL.'/departments/updateDepartment.php?department_id='.$department->getId(),
						'edit'
					);
				}
				$name = View::escape($department->getName());
				$user = View::escape($department->getDefault_user()->getUsername());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td>$user</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
