<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param IssueList $this->issueList
 */
?>
<div class="issueList">
	<h1>Associated Issues</h1>
	<div class="issue">
	<?php
		foreach ($this->issueList as $issue) {
			$editButton = '';
			$responseButton = '';
			if (userIsAllowed('Issues')) {
				$editButton = $this->template->formButton(
					'Edit Issue',
					'button',
					'edit',
					null,
					"document.location.href='".BASE_URL."/issues/updateIssue.php?issue_id={$issue->getId()}';"
				);
				$responseButton = $this->template->formButton(
					'Add Response',
					'button',
					'add',
					null,
					BASE_URL.'/issues/addResponse.php?issue_id='.$issue->getId()
				);
			}


			$enteredBy = '';
			if ($issue->getEnteredByPerson()) {
				$href = BASE_URL.'/people/viewPerson.php?person_id='.$issue->getEnteredByPerson_id();
				$enteredBy = View::escape($issue->getEnteredByPerson()->getUsername());
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
			$notes = View::escape($issue->getNotes());
			echo "
			<h1><span class=\"date\">{$issue->getDate()}</span>
				<span class=\"issueType\">$type</span>
				$editButton
				$responseButton
			</h1>
			<table>
				<thead>
					<tr><th>Created by</th>
						<th>Categories</th>
						<th>Reported By</th>
						<th>Contacted By</th>
						<th>Case #</th>
						<th>Comments</th>
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
						<td>$notes</td>
					</tr>
				</tbody>
			</table>
			";
		}
	?>
</div>