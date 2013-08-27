<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department $this->department
 */
$editButton = '';
$deleteButton = '';
if (userIsAllowed('departments','update')) {
	$editButton = "
	<a class=\"btn\"
		href=\"".BASE_URL."/departments/update?department_id={$this->department->getId()}\">
		<span class=\"icon-pencil\"></span>
		Edit
	</a>
	";
	// Make sure this would not violate any foreign keys
	if ($this->department->isSafeToDelete()) {
		$deleteButton = "
		<a class=\"btn\"
			href=\"".BASE_URL."/departments/delete?department_id={$this->department->getId()}\">
			<span class=\"icon-remove\"></span>
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
	$categories[] = View::escape($category->getName());
}
$categories = implode(', ',$categories);

$actions = array();
foreach ($this->department->getActions() as $action) {
	$actions[] = View::escape($action->getName());
}
$actions = implode(', ',$actions);

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
	</table>
</div>
";
