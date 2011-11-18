<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DepartmentList $this->departmentList
 */
?>
<div class="departmentList">
	<h1><?php
			if (userIsAllowed('Departments')) {
				echo $this->template->linkButton(
					'Add',BASE_URL.'/departments/updateDepartment.php','add'
				);
			}
		?>
		Departments
	</h1>
	<?php
		foreach ($this->departmentList as $department) {
			$editButton = '';
			$deleteButton = '';
			if (userIsAllowed('Departments')) {
				$editButton = $this->template->linkButton(
					'Edit',
					BASE_URL."/departments/updateDepartment.php?department_id={$department->getId()}",
					'edit'
				);
				// Departments should only be deleted when there's no people in them
				if (!count($department->getPeople())) {
					$deleteButton = $this->template->linkButton(
						'Delete',
						BASE_URL."/departments/deleteDepartment.php?department_id={$department->getId()}",
						'delete'
					);
				}
			}
			$name = View::escape($department->getName());

			$defaultPerson = $department->getDefaultPerson();
			if ($defaultPerson) {
				$defaultPerson = View::escape($defaultPerson->getFullname());
			}

			$categories = array();
			foreach ($department->getCategories() as $category) {
				$categories[] = View::escape($category['name']);
			}
			$categories = implode(', ',$categories);

			$actions = array();
			foreach ($department->getActions() as $action) {
				$actions[] = View::escape($action['name']);
			}
			$actions = implode(', ',$actions);

			$statuses = array();
			foreach ($department->getCustomStatuses() as $status) {
				$statuses[] = View::escape($status);
			}
			$statuses = implode(', ',$statuses);

			echo "
			<div class=\"department\">
				<h3>$name $editButton $deleteButton</h3>
				<table>
					<tr><th>Default Person</th><td>$defaultPerson</td></tr>
					<tr><th>Categories</th><td>$categories</td></tr>
					<tr><th>Actions</th><td>$actions</td></tr>
					<tr><th>Statuses</th><td>$statuses</td></tr>
				</table>
			</div>
			";
		}
	?>
</div>
