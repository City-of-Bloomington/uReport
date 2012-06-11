<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->data
 */
include APPLICATION_HOME.'/blocks/html/reports/parameters.inc';
$h = $this->template->getHelper('formatDuration');
?>
<div class="searchResultsRows">
	<h2>Assignments Report</h2>
	<p>List people who are currently assigned tickets.</p>
<?php
foreach ($this->data as $id=>$o) {
	$first = View::escape($o['person']['firstname']);
	$last = View::escape($o['person']['lastname']);
	echo "
	<div>
		<h2><a href=\"".BASE_URL."/people/view?person_id=$id\">$first $last</a></h2>
		<table>
			<thead><tr><th>Category</th><th>Status</th><th>Count</th><th>Avg Time Until Close</th></thead>
			<tbody>
	";
	foreach ($o['categories'] as $cid=>$c) {
		foreach (array('open','closed') as $status) {
			$name = View::escape($c['name']);
			if (isset($c[$status])) {
				$avg = $h->formatDuration($c[$status]['seconds']);
				echo "
				<tr><th>$name</th>
					<th>$status</th>
					<td>{$c[$status]['count']}</td>
					<td>$avg</td>
				</tr>
				";
			}
		}
	}
	echo "
			</tbody>
		</table>
	</div>
	";
}
?>
</div>