<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DepartmentList $this->departmentList
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
				<th>Default Person</th>
				<th>Categories</th>
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
				$person = View::escape($department->getDefault_person()->getFullname());
				echo "
				<tr><td>$editButton</td>
					<td>$name</td>
					<td>$person</td>
					<td>
				";
					if ($department->hasCategories()) {
						echo '<ul>';
						foreach ($department->getCategories() as $category) {
							$name = View::escape($category);
							echo "<li>$name</li>";
						}
						echo '</ul>';
					}
				echo "
					</td>
				</tr>
				";
			}
		?>
		</tbody>
	</table>
</div>
