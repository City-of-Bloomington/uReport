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
	$addButton = $this->template->linkButton(
		'Add Issue',
		BASE_URL."/issues/updateIssue.php?ticket_id={$this->ticket->getId()}';",
		'add'
	);
}
echo "
<div class=\"issueList\">
	<h1>Associated Issues</h1>
	$addButton
	<div class=\"issue\">
";
	foreach ($this->issueList as $issue) {
		$editButton = '';
		$responseButton = '';
		if (!$this->disableButtons && userIsAllowed('Issues')) {
			$editButton = $this->template->linkButton(
				'Edit Issue',
				BASE_URL."/issues/updateIssue.php?issue_id={$issue->getId()}';",
				'edit',
				null,
				null,
				'button'
			);

			if ($issue->getReportedByPerson_id()) {
				$responseButton = $this->template->linkButton(
					'Add Response',
					BASE_URL."/issues/addResponse.php?issue_id={$issue->getId()}';",
					'add',
					null,
					null,
					'button'
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
		$responseMethod = View::escape($issue->getResponseMethod());
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
					<th>Contacted Via</th>
					<th>Respond Via</th>
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
					<td>$responseMethod</td>
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

		$block = new Block('media/mediaList.inc',array(
			'issue'=>$issue,
			'mediaList'=>$issue->getMedia(),
			'disableButtons'=>$this->disableButtons)
		);
		echo $block->render('html',$this->template);

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