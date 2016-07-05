<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Issue $this->issue
 * @param bool  $this->disableButtons
 */
use Application\Models\Person;

$editButton     = '';
if (!$this->disableButtons && Person::isAllowed('issues','update')) {
	$helper = $this->template->getHelper('buttonLink');
	$editButton = $helper->buttonLink(
		BASE_URI.'/issues/update?issue_id='.$this->issue->getId(),
		$this->_('edit_issue'),
		'edit'
	);
}
$url = BASE_URL."/issues?issue_id={$this->issue->getId()}";
$type = self::escape($this->issue->getIssueType());
echo "
<header class=\"issueHeader\">
	<h1><span class=\"date\"><a href=\"$url\">{$this->issue->getDate(DATE_FORMAT)}</a></span>
		<span class=\"issueType\"><a href=\"$url\">$type</a></span>
    </h1>
    <div class=\"tools\">$editButton</div>
</header>
";
