<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department $this->department
 */
use Application\Models\Person;
use Blossom\Classes\View;

$editButton   = '';
$deleteButton = '';
if (Person::isAllowed('departments','update')) {
	$helper = $this->template->getHelper('buttonLink');
	$editButton = $helper->buttonLink(
		BASE_URI.'/departments/update?department_id='.$this->department->getId(),
		$this->_('labels.edit'),
		'edit'
	);
	// Make sure this would not violate any foreign keys
	if ($this->department->isSafeToDelete()) {
		$deleteButton = $helper->buttonLink(
			BASE_URI.'/departments/delete?department_id='.$this->department->getId(),
			$this->_('labels.delete'),
			'delete'
		);
	}
}
$name = self::escape($this->department->getName());

$defaultPerson = $this->department->getDefaultPerson();
if ($defaultPerson) {
	$defaultPerson = self::escape($defaultPerson->getFullname());
}

$categories = array();
foreach ($this->department->getCategories() as $category) {
	$categories[] = self::escape($category->getName());
}
$categories = implode(', ',$categories);

$actions = array();
foreach ($this->department->getActions() as $action) {
	$actions[] = self::escape($action->getName());
}
$actions = implode(', ',$actions);

echo "
<div class=\"department\">
	<h3><a href=\"".BASE_URI."/departments/view?department_id={$this->department->getId()}\">
			$name
		</a>
		$editButton $deleteButton
	</h3>
	<table>
		<tr><th>{$this->_('labels.defaultPerson')}</th>
			<td>$defaultPerson</td></tr>
		<tr><th>{$this->_(array('labels.category', 'labels.categories', 2))}</th>
			<td>$categories</td></tr>
		<tr><th>{$this->_(array('labels.action', 'labels.actions', 2))}</th>
			<td>$actions</td></tr>
	</table>
</div>
";
