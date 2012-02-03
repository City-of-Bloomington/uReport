<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department $this->department
 */
$editButton = '';
$deleteButton = '';
if (userIsAllowed('departments','update')) {
	$editButton = "
	<a class=\"edit button\"
		href=\"".BASE_URL."/departments/update?department_id={$this->department->getId()}\">
		Edit
	</a>
	";
	// Departments should only be deleted when there's no people in them
	if (!count($this->department->getPeople())) {
		$deleteButton = "
		<a class=\"delete button\"
			href=\"".BASE_URL."/departments/delete?department_id={$this->department->getId()}\">
			Delete
		</a>
		";
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
	<h3><a href=\"".BASE_URL."/departments/view?department_id={$this->department->getId()}\">
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
