<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param IssueList $this->issueList
 * @param Ticket $this->ticket (optional)
 * @param bool $this->disableButtons
 * @param bool $this->disableComments
 */
$addButton = '';
if (!$this->disableButtons && $this->ticket && userIsAllowed('Issues')) {
	$addButton = $this->template->formButton(
		'Add Issue','button','add',null,
		"document.location.href='".BASE_URL."/issues/updateIssue.php?ticket_id={$this->ticket->getId()}';"
	);
}
echo "
<div class=\"issueList\">
	<h1>Associated Issues $addButton</h1>
	<div class=\"issue\">
";
	foreach ($this->issueList as $issue) {
		$editButton = '';
		$responseButton = '';
		if (!$this->disableButtons && userIsAllowed('Issues')) {
			$editButton = $this->template->formButton(
				'Edit Issue',
				'button',
				'edit',
				null,
				"document.location.href='".BASE_URL."/issues/updateIssue.php?issue_id={$issue->getId()}';"
			);

			if ($issue->getReportedByPerson_id()) {
				$responseButton = $this->template->formButton(
					'Add Response',
					'button',
					'add',
					null,
					"document.location.href='".BASE_URL."/issues/addResponse.php?issue_id={$issue->getId()}';"
				);
			}
		}


		$enteredBy = '';
		if ($issue->getEnteredByPerson()) {
			$href = BASE_URL.'/people/viewPerson.php?person_id='.$issue->getEnteredByPerson_id();
			$enteredBy = View::escape($issue->getEnteredByPerson()->getFullname());
			$enteredBy = "<a href=\"$href\">$enteredBy</a>";
		}

		$reportedBy = '';
		if ($issue->getReportedByPerson()) {
			$href = BASE_URL.'/people/viewPerson.php?person_id='.$issue->getReportedByPerson_id();
			$reportedBy = View::escape($issue->getReportedByPerson()->getFullname());
			$reportedBy = "<a href=\"$href\">$reportedBy</a>";
		}

		$type = View::escape($issue->getIssueType());
		$contactMethod = View::escape($issue->getContactMethod());
		$caseNumber = View::escape($issue->getCase_number());
		echo "
		<h2><span class=\"date\">{$issue->getDate()}</span>
			<span class=\"issueType\">$type</span>
			$editButton
			$responseButton
		</h2>
		<table>
			<thead>
				<tr><th>Created by</th>
					<th>Categories</th>
					<th>Reported By</th>
					<th>Contacted By</th>
					<th>Case #</th>
		";
					if (!$this->disableComments) {
						echo '<th>Comments</th>';
					}
		echo "
				</tr>
			</thead>
			<tbody>
				<tr><td>$enteredBy</td>
					<td>
		";
					if ($issue->hasCategories()) {
						echo '<ul>';
						foreach ($issue->getCategories() as $category) {
							$name = View::escape($category);
							echo "<li>$name</li>";
						}
						echo '</ul>';
					}
		echo "
					</td>
					<td>$reportedBy</td>
					<td>$contactMethod</td>
					<td>$caseNumber</td>
		";
					if (!$this->disableComments) {
						$notes = View::escape($issue->getNotes());
						echo "<td>$notes</td>";
					}
		echo "
				</tr>
			</tbody>
		</table>
		";

		$history = $issue->getHistory();
		if (count($history)) {
			$block = new Block(
				'tickets/history.inc',
				array('ticketHistory'=>$history,'disableComments'=>$this->disableComments)
			);
			echo $block->render('html',$this->template);
		}
	}
echo "
	</div>
</div>
";