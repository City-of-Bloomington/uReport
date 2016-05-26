<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Category $this->category
 */
use Application\Models\Issue;
use Application\Models\Person;

$name = self::escape($this->category->getName());
$description = self::escape($this->category->getDescription());

$g = $this->category->getCategoryGroup();
$group = $g ? self::escape($g->getName()) : '';

$d = $this->category->getDepartment();
$department = $d ? self::escape($d->getName()) : '';

$autoCloseActive    = $this->category->autoCloseIsActive   () ? $this->_('labels.active_yes') : $this->_('labels.active_no');

$c = $this->category->getCustomFields();

$editButton = '';
if (Person::isAllowed('categories','update')) {
    $helper = $this->template->getHelper('buttonLink');
    $editButton = $helper->buttonLink(
        BASE_URI.'/categories/update?category_id='.$this->category->getId(),
        $this->_('labels.edit'),
        'edit'
    );
}
echo "
<div class=\"categoryInfo\">
	<h2>$name $editButton</h2>
	<p>$description</p>
	<table>
		<tr><th>Category Group</th>
			<td>$group</td>
		</tr>
		<tr><th>Default Department</th>
			<td>$department</td>
		</tr>
		<tr><th>{$this->_('labels.displayPermissionLevel')}</th>
			<td>{$this->category->getDisplayPermissionLevel()}</td>
		</tr>
		<tr><th>{$this->_('labels.postingPermissionLevel')}</th>
			<td>{$this->category->getPostingPermissionLevel()}</td>
		</tr>

        <tr><th>{$this->_('labels.autoCloseIsActive')}</th>
            <td>$autoCloseActive</td>
        </tr>
	</table>


	<form method=\"get\">
		<fieldset><legend>{$this->_('labels.custom_fields')}</legend>
";
			$this->issue = new Issue();
			include APPLICATION_HOME.'/blocks/html/tickets/issueCustomFields.inc';
echo "
		</fieldset>
	</form>
</div>
";
