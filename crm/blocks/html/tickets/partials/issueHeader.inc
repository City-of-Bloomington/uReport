<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Issue $this->ticket
 * @param bool  $this->disableButtons
 */
use Application\Models\Person;

$editButton     = '';
if (!$this->disableButtons && Person::isAllowed('issues','update')) {
	$helper = $this->template->getHelper('buttonLink');
	$editButton = $helper->buttonLink(
		BASE_URI.'/issues/update?issue_id='.$this->ticket->getId(),
		$this->_('edit_issue'),
		'edit'
	);
}
$url = $this->ticket->getURL();
$type = self::escape($this->ticket->getIssueType());
echo "
<header class=\"issueHeader\">
	<h1><span class=\"date\"><a href=\"$url\">{$this->ticket->getDate(DATE_FORMAT)}</a></span>
		<span class=\"issueType\"><a href=\"$url\">$type</a></span>
    </h1>
    <div class=\"tools\">$editButton</div>
</header>
";
