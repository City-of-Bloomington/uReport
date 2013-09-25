<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 * @param bool  $this->disableButtons
 */
$editButton     = '';
$deleteButton   = '';

if (!$this->disableButtons && userIsAllowed('issues','update')) {
	$editButton = "
	<a class=\"btn\"
		href=\"".BASE_URI."/issues/update?issue_id={$this->issue->getId()}\">
		<span class=\"icon-pencil\"></span>
		{$this->translate('labels.edit_issue')}
	</a>
	";
	$deleteButton = "
	<a class=\"btn\"
		href=\"".BASE_URI."/issues/delete?issue_id={$this->issue->getId()}\">
		<span class=\"icon-remove\"></span>
		{$this->translate('labels.delete_issue')}
	</a>
	";
}
$url = BASE_URL."/issues?issue_id={$this->issue->getId()}";
$type = View::escape($this->issue->getIssueType());
echo "
<div class=\"issueHeader\">
	<h2><span class=\"date\"><a href=\"$url\">{$this->issue->getDate(DATE_FORMAT)}</a></span>
		<span class=\"issueType\"><a href=\"$url\">$type</a></span>
		<span class=\"issueActions\">
			$editButton
			$deleteButton
		</span>
	</h2>
</div>
";
