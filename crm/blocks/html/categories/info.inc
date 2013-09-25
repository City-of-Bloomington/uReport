<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Category $this->category
 */
$name = View::escape($this->category->getName());
$description = View::escape($this->category->getDescription());

$g = $this->category->getCategoryGroup();
$group = $g ? View::escape($g->getName()) : '';

$d = $this->category->getDepartment();
$department = $d ? View::escape($d->getName()) : '';

$c = $this->category->getCustomFields();
echo "
<div class=\"categoryInfo\">
	<h2>$name</h2>
	<p>$description</p>
	<table>
		<tr><th>Category Group</th>
			<td>$group</td>
		</tr>
		<tr><th>Default Department</th>
			<td>$department</td>
		</tr>
		<tr><th>{$this->translate('labels.displayPermissionLevel')}</th>
			<td>{$this->category->getDisplayPermissionLevel()}</td>
		</tr>
		<tr><th>{$this->translate('labels.postingPermissionLevel')}</th>
			<td>{$this->category->getPostingPermissionLevel()}</td>
		</tr>
	</table>
	<form method=\"get\">
		<fieldset><legend>{$this->translate('labels.custom_fields')}</legend>
";
			$this->issue = new Issue();
			include APPLICATION_HOME.'/blocks/html/tickets/issueCustomFields.inc';
echo "
		</fieldset>
	</form>
</div>
";
