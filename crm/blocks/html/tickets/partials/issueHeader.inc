<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Issue $this->issue
 * @param bool  $this->disableButtons
 */
use Application\Models\Person;
use Blossom\Classes\View;

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
<div class=\"issueHeader\">
	<h2><span class=\"date\"><a href=\"$url\">{$this->issue->getDate(DATE_FORMAT)}</a></span>
		<span class=\"issueType\"><a href=\"$url\">$type</a></span>
		<span class=\"issueActions\">
			$editButton
		</span>
	</h2>
</div>
";
