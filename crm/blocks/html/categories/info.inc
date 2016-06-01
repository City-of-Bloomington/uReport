<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Category $this->category
 */
use Application\Models\Issue;
use Application\Models\Person;

$fields = [
    'id', 'name', 'description', 'categoryGroup', 'department',
    'displayPermissionLevel', 'postingPermissionLevel', 'notificationReplyEmail'
];
foreach ($fields as $f) {
    $get = 'get'.ucfirst($f);
    $$f  = self::escape($this->category->$get());
}

$autoCloseActive = $this->category->autoCloseIsActive()
    ? $this->_('labels.active_yes')
    : $this->_('labels.active_no');


$editButton = '';
if (Person::isAllowed('categories', 'update')) {
    $helper = $this->template->getHelper('buttonLink');
    $editButton = $helper->buttonLink(
        BASE_URI."/categories/update?category_id=$id",
        $this->_('labels.edit'),
        'edit'
    );
}
echo "
<section class=\"categoryInfo\">
    <header>
        <h1>$name</h1>
        <div class=\"tools\">$editButton</div>
    </header>
	<p>$description</p>
	<table>
		<tr><th>{$this->_('labels.categoryGroup')}</th>
			<td>$categoryGroup</td>
		</tr>
		<tr><th>{$this->_('labels.department')}</th>
			<td>$department</td>
		</tr>
		<tr><th>{$this->_('labels.displayPermissionLevel')}</th>
			<td>$displayPermissionLevel</td>
		</tr>
		<tr><th>{$this->_('labels.postingPermissionLevel')}</th>
			<td>$postingPermissionLevel</td>
		</tr>
        <tr><th>{$this->_('labels.replyEmail')}</th>
            <td>$notificationReplyEmail</td>
        </tr>
        <tr><th>{$this->_('labels.autoCloseIsActive')}</th>
            <td>$autoCloseActive</td>
        </tr>
	</table>
";
    $this->_include('responseTemplates/list.inc');

echo "
	<form method=\"get\">
		<fieldset><legend>{$this->_('labels.custom_fields')}</legend>
";
			$this->issue = new Issue();
			$this->_include('tickets/issueCustomFields.inc');
echo "
		</fieldset>
	</form>
</section>
";
