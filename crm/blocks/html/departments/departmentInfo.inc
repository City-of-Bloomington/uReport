<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department $this->department
 */
use Application\Models\Person;
use Blossom\Classes\View;

$id = $this->department->getId();

$editButton   = '';
$deleteButton = '';
if (Person::isAllowed('departments','update')) {
	$helper = $this->template->getHelper('buttonLink');

	$editButton = $helper->buttonLink(
		BASE_URI.'/departments/update?department_id='.$id,
		$this->_('edit'),
		'edit'
	);
	// Make sure this would not violate any foreign keys
	if ($this->department->isSafeToDelete()) {
		$deleteButton = $helper->buttonLink(
			BASE_URI.'/departments/delete?department_id='.$id,
			$this->_('delete'),
			'delete'
		);
	}
}
$name = self::escape($this->department->getName());

$defaultPerson = $this->department->getDefaultPerson();
if ($defaultPerson) {
	$defaultPerson = self::escape($defaultPerson->getFullname());
}

$categories = [];
foreach ($this->department->getCategories() as $category) {
	$categories[] = self::escape($category->getName());
}
$categories = implode(', ',$categories);

$actions = [];
foreach ($this->department->getActions() as $action) {
	$actions[] = self::escape($action->getName());
}
$actions = implode(', ',$actions);

$uri = BASE_URI."/departments/view?department_id=$id";
?>
<article class="department">
    <header>
        <h1><a href="<?= $uri; ?>"><?= $name; ?></a></h1>
        <div class="tools">
            <?= "$editButton $deleteButton"; ?>
        </div>
    </header>

    <dl><dt><?= $this->_('defaultPerson'); ?></dt>
        <dd><?= $defaultPerson; ?></dd>
    </dl>
    <dl><dt><?= $this->_(['category', 'categories', 2]); ?></dt>
        <dd><?= $categories; ?></dd>
    </dl>
    <dl><dt><?= $this->_(['action', 'actions', 2]); ?></dt>
        <dd><?= $actions; ?></dd>
    </dl>
</article>
