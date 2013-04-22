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
	<a class=\"edit button\"
		href=\"".BASE_URL."/issues/update?issue_id={$this->issue->getId()}\">
		Edit Issue
	</a>
	";
	$deleteButton = "
	<a class=\"delete button\"
		href=\"".BASE_URL."/issues/delete?issue_id={$this->issue->getId()}\">
		Delete Issue
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
