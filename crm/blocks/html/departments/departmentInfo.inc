<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department $this->department
 */
$editButton = '';
$deleteButton = '';
if (userIsAllowed('Departments')) {
	$editButton = $this->template->linkButton(
		'Edit',
		BASE_URL."/departments/updateDepartment.php?department_id={$this->department->getId()}",
		'edit'
	);
	// Departments should only be deleted when there's no people in them
	if (!count($this->department->getPeople())) {
		$deleteButton = $this->template->linkButton(
			'Delete',
			BASE_URL."/departments/deleteDepartment.php?department_id={$this->department->getId()}",
			'delete'
		);
	}
}
$name = View::escape($this->department->getName());

$defaultPerson = $this->department->getDefaultPerson();
if ($defaultPerson) {
	$defaultPerson = View::escape($defaultPerson->getFullname());
}

$categories = array();
foreach ($this->department->getCategories() as $category) {
	$categories[] = View::escape($category['name']);
}
$categories = implode(', ',$categories);

$actions = array();
foreach ($this->department->getActions() as $action) {
	$actions[] = View::escape($action['name']);
}
$actions = implode(', ',$actions);

$statuses = array();
foreach ($this->department->getCustomStatuses() as $status) {
	$statuses[] = View::escape($status);
}
$statuses = implode(', ',$statuses);

echo "
<div class=\"department\">
	<h3><a href=\"".BASE_URL."/departments/viewDepartment.php?department_id={$this->department->getId()}\">
			$name
		</a>
		$editButton $deleteButton
	</h3>
	<table>
		<tr><th>Default Person</th><td>$defaultPerson</td></tr>
		<tr><th>Categories</th><td>$categories</td></tr>
		<tr><th>Actions</th><td>$actions</td></tr>
		<tr><th>Statuses</th><td>$statuses</td></tr>
	</table>
</div>
";
