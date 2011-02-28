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
<table>
	<tbody>
	<?php
		foreach ($this->issueList as $issue) {
			if ($issue->getPerson()) {
				$href = BASE_URL.'/people/viewPerson.php?person_id='.$issue->getPerson_id();
				$username = View::escape($issue->getPerson()->getUsername());
				$username = "<a href=\"$href\">$username</a>";
			}
			else {
				$username = '';
			}
			if ($issue->getConstituent()) {
				$href = BASE_URL.'/people/viewPerson.php?person_id='.$issue->getConstituent_id();
				$constituent = View::escape($issue->getConstituent()->getFullname());
				$constituent = "<a href=\"$href\">$constituent</a>";
			}
			else {
				$constituent = '';
			}

			$type = View::escape($issue->getIssueType());
			$contactMethod = View::escape($issue->getContactMethod());
			$caseNumber = View::escape($issue->getCase_number());
			$notes = View::escape($issue->getNotes());
			echo "
			<tr><td class=\"date\">{$issue->getDate('m-d-y')}</td>
				<td class=\"issueType\">$type</td>
				<td>Edit Issue</td>
				<td>Add Response</td>
			</tr>
			</tbody>
			</table>
			<table>
			<thead>
				<tr>
					<th>Created by</th>
					<th>Categories</th>
					<th>Reported By</th>
					<th>Contacted By</th>
					<th>Case #</th>
					<th>Comments</th>
				</tr>
			</thead>
			<tbody>
				<tr><td>$username</td>
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
				<td>$constituent</td>
				<td>$contactMethod</td>
				<td>$caseNumber</td>
				<td>$notes</td>
			</tr>
			";
		}
	?>
	</tbody>
</table>
</div>