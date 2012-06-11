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
	<h2>Categories Report</h2>
	<?php
		foreach ($this->data as $cid=>$o) {
			$name = View::escape($o['category']['name']);
			echo "
			<div><h2>$name</h2>
				<table>
					<thead><tr><th>Person</th><th>Status</th><th>Count</th><th>Avg Time Until Close</th></thead>
					<tbody>
			";
			foreach ($o['people'] as $pid=>$p) {
				$name = View::escape("$p[firstname] $p[lastname]");
				foreach (array('open', 'closed') as $status) {
					if (isset($p[$status])) {
						$avg = $h->formatDuration($p[$status]['seconds']);
						echo "
						<tr><th><a href=\"".BASE_URI."/people/view?person_id=$pid\">$name</a></th>
							<th>$status</th>
							<td>{$p[$status]['count']}</td>
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