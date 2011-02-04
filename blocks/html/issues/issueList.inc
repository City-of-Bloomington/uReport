<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param IssueList $this->issueList
 */
?>
<div class="issueList">
<h1>Issues</h1>
<table>
	<thead>
		<tr><th>Date</th>
			<th>Type</th>
			<th>Categories</th>
			<th>Entered By</th>
			<th>Reported By</th>
			<th>Contacted By</th>
			<th>Comments</th>
		</tr>
	</thead>
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
			$notes = View::escape($issue->getNotes());
			echo "
			<tr><td>{$issue->getDate('Y-m-d')}</td>
				<td>$type</td>
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
				<td>$username</td>
				<td>$constituent</td>
				<td>$contactMethod</td>
				<td>$notes</td>
			</tr>
			";
		}
	?>
	</tbody>
</table>
</div>