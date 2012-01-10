<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
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
if (!$this->disableButtons && userIsAllowed('Issues')) {
	$editButton = $this->template->linkButton(
		'Edit Issue',
		BASE_URL."/tickets/updateIssue.php?ticket_id={$this->ticket->getId()};index={$this->index}",
		'edit',null,null,'button'
	);
	$deleteButton = $this->template->linkButton(
		'Delete Issue',
		BASE_URL."/tickets/deleteIssue.php?ticket_id={$this->ticket->getId()};index={$this->index}",
		'delete',null,null,'button'
	);

	if ($this->issue->getReportedByPerson()) {
		$responseButton = $this->template->linkButton(
			'Add Response',
			BASE_URL."/tickets/addResponse.php?ticket_id={$this->ticket->getId()};index=$this->index",
			'add',null,null,'button'
		);
	}
}
$url = BASE_URL."/tickets/viewIssue.php?ticket_id={$this->ticket->getId()};index={$this->index}";
$type = View::escape($this->issue->getType());
echo "
<div class=\"issueInfo\">
	<h2><span class=\"date\"><a href=\"$url\">{$this->issue->getDate(DATE_FORMAT)}</a></span>
		<span class=\"issueType\"><a href=\"$url\">$type</a></span>
		$editButton
		$responseButton
		$deleteButton
	</h2>
";
	if (!$this->disableComments) {
		$description = View::escape($this->issue->getDescription());
		echo "<div class=\"notes\">$description</div>";
	}

	// Don't display any personal information from the issue unless the user is authenticated
	if (userIsAllowed('People')) {
		$enteredBy = $this->issue->getEnteredByPerson();
		if ($enteredBy) {
			$href = BASE_URL."/people/viewPerson.php?person_id={$enteredBy->getId()}";
			$enteredBy = View::escape($enteredBy->getFullname());
			$enteredBy = "<a href=\"$href\">$enteredBy</a>";
		}

		$reportedBy = $this->issue->getReportedByPerson();
		if ($reportedBy) {
			$href = BASE_URL."/people/viewPerson.php?person_id={$reportedBy->getId()}";
			$reportedBy = View::escape($reportedBy->getFullname());
			$reportedBy = "<a href=\"$href\">$reportedBy</a>";
		}

		$contactMethod = View::escape($this->issue->getContactMethod());
		$responseMethod = View::escape($this->issue->getResponseMethod());
		$labels = View::escape(implode(', ',$this->issue->getLabels()));
		echo "
		<table>
			<thead>
				<tr><th>Created by</th>
					<th>Reported By</th>
					<th>Contacted Via</th>
					<th>Respond Via</th>
					<th>Labels</th>
				</tr>
			</thead>
			<tbody>
				<tr><td>$enteredBy</td>
					<td>$reportedBy</td>
					<td>$contactMethod</td>
					<td>$responseMethod</td>
					<td>$labels</td>
				</tr>
			</tbody>
		</table>
		";
	}
	$customFields = $this->ticket->getData('category.customFields');
	if ($customFields) {
		$customFieldData = $this->issue->getCustomFields();
		echo "<table>";
		foreach ($customFields as $definition) {
			$label = isset($definition['label']) ? View::escape($definition['label']) : View::escape($definition['name']);
			$value = isset($customFieldData[$definition['name']]) ? $customFieldData[$definition['name']] : '';
			$value = is_array($value) ? implode(', ',$value) : View::escape($value);
			echo "<tr><th>$label</th><td>$value</td></tr>";
		}
		echo "</table>";
	}

	// Don't display any personal information from the issue unless the user is authenticated
	if (userIsAllowed('People')) {
		$this->responses = $this->issue->getResponses();
		if ($this->responses) {
			include APPLICATION_HOME.'/blocks/html/tickets/responseList.inc';
		}
	}

	$this->mediaList = $this->issue->getMedia();
	include APPLICATION_HOME.'/blocks/html/media/mediaList.inc';

	$this->history = $this->issue->getHistory();
	if (count($this->history)) {
		include APPLICATION_HOME.'/blocks/html/tickets/history.inc';
	}

echo "</div>";