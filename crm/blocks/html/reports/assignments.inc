<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->data
 */
use Blossom\Classes\View;

include APPLICATION_HOME.'/blocks/html/reports/parameters.inc';
$h = $this->template->getHelper('formatDuration');
?>
<div class="searchResultsRows">
	<h2>Assignments Report</h2>
	<p>List people and their assigned tickets.</p>
	<?php
		foreach ($this->data as $pid=>$p) {
			$person = View::escape($p['name']);
			$open = 0;
			$closed = 0;
			echo "
			<div><h3>$person</h3>
				<table>
					<thead>
						<tr><th></th>
							<th>Open</th>
							<th>Closed</th>
							<th>Substatus</th>
							<th>Totals</th>
						</tr>
					</thead>
					<tbody>
			";
				foreach ($p['categories'] as $cid=>$c) {
					$counts = array('open'=>0, 'closed'=>0, 'substatus'=>array());
					foreach ($c['data'] as $r) {
						if ($r['substatus']) {
							$counts['substatus'][] = "$r[substatus]: $r[count]";
						}
						$counts[$r['status']] += $r['count'];
						if (in_array($r['status'], array('open','closed'))) {
							$$r['status'] += $r['count'];
						}
					$substatus = $counts['substatus']
						? implode(', ', $counts['substatus'])
						: '';
					}
					$category = View::escape($c['name']);
					$total = $counts['open'] + $counts['closed'];
					echo "
					<tr><th>$category</th>
						<td>$counts[open]</td>
						<td>$counts[closed]</td>
						<td>$substatus</td>
						<td>$total</td>
					</tr>
					";
				}
			$total = $open + $closed;
			echo "
					<tr class=\"totals\"><td></td>
						<td>$open</td>
						<td>$closed</td>
						<td></td>
						<td>$total</td>
					</tr>
					</tbody>
				</table>
			</div>
			";
		}
	?>
</div>
