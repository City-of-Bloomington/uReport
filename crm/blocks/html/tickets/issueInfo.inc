<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param Issue $this->issue
 * @param int $this->index
 * @param bool $this->disableButtons
 */
$editButton = '';
$deleteButton = '';
$responseButton = '';
$uploadButton = '';
if (!$this->disableButtons && userIsAllowed('issues','update')) {
	$editButton = "
	<a class=\"edit button\"
		href=\"".BASE_URL."/issues/update?ticket_id={$this->ticket->getId()};index={$this->index}\">
		Edit Issue
	</a>
	";
	$deleteButton = "
	<a class=\"delete button\"
		href=\"".BASE_URL."/issues/delete?ticket_id={$this->ticket->getId()};index={$this->index}\">
		Delete Issue
	</a>
	";

	if ($this->issue->getReportedByPerson()) {
		$responseButton = "
		<a class=\"add button\"
			href=\"".BASE_URL."/issues/respond?ticket_id={$this->ticket->getId()};index={$this->index}\">
			Add Response
		</a>
		";
	}
	if (userIsAllowed('media','upload')) {
		$uploadButton = "
		<a class=\"upload button\"
			href=\"".BASE_URL."/media/upload?ticket_id={$this->ticket->getId()};index={$this->index}\">
			Upload Attachment
		</a>
		";
	}
}
$url = BASE_URL."/issues?ticket_id={$this->ticket->getId()};index={$this->index}";
$type = View::escape($this->issue->getType());
echo "
<div class=\"issueInfo\">
	<div class=\"issueHeader\">
		<h2><span class=\"date\"><a href=\"$url\">{$this->issue->getDate(DATE_FORMAT)}</a></span>
			<span class=\"issueType\"><a href=\"$url\">$type</a></span>
			<span class=\"issueActions\">
				$editButton
				$responseButton
				$deleteButton
				$uploadButton
			</span>
		</h2>
	</div>
";
	// Don't display any personal information from the issue unless the user is authenticated
	if (userIsAllowed('people','view')) {
		$enteredBy = $this->issue->getEnteredByPerson();
		if ($enteredBy) {
			$href = BASE_URL."/people/view?person_id={$enteredBy->getId()}";
			$enteredBy = View::escape($enteredBy->getFullname());
			$enteredBy = "<a href=\"$href\">$enteredBy</a>";
		}

		$reportedBy = $this->issue->getReportedByPerson();
		if ($reportedBy) {
			$href = BASE_URL."/people/view?person_id={$reportedBy->getId()}";
			$reportedBy = View::escape($reportedBy->getFullname());
			$reportedBy = "<a href=\"$href\">$reportedBy</a>";
		}

		$contactMethod = View::escape($this->issue->getContactMethod());
		$responseMethod = View::escape($this->issue->getResponseMethod());
		$labels = View::escape(implode(', ',$this->issue->getLabels()));
		echo "
		<ul><li><label>Created By:</label> $enteredBy</li>
			<li><label>Reported By:</label> $reportedBy</li>
			<li><label>Contacted Via:</label> $contactMethod</li>
			<li><label>Respond Via:</label> $responseMethod</li>
			<li><label>Labels:</label> $labels</li>
		";
			$customFields = $this->ticket->getData('category.customFields');
			if ($customFields) {
				$customFieldData = $this->issue->getCustomFields();
				foreach ($customFields as $definition) {
					$label = isset($definition['label']) ? View::escape($definition['label']) : View::escape($definition['name']);
					$value = isset($customFieldData[$definition['name']]) ? $customFieldData[$definition['name']] : '';
					$value = is_array($value) ? implode(', ',$value) : View::escape($value);
					echo "<li><label>$label:</label> $value</li>\n";
				}
			}
		echo "
		</ul>
		";
	}

	if (!$this->disableComments) {
		$description = View::escape($this->issue->getDescription());
		echo "<div class=\"notes\"><h2>Details:</h2>$description</div>";
	}
	$this->mediaList = $this->issue->getMedia();
	include APPLICATION_HOME.'/blocks/html/media/mediaList.inc';

	// Don't display any personal information from the issue unless the user is authenticated
	if (userIsAllowed('people','view')) {
		$this->responses = $this->issue->getResponses();
		if ($this->responses) {
			include APPLICATION_HOME.'/blocks/html/tickets/responseList.inc';
		}
	}

	$this->history = $this->issue->getHistory();
	if (count($this->history)) {
		include APPLICATION_HOME.'/blocks/html/tickets/history.inc';
	}

echo "
</div>
";