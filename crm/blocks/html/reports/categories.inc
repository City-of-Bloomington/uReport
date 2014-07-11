<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->data
 *
 * select c.id as category_id, c.name as category,
 * t.assignedPerson_id, t.status, t.resolution_id,
 * p.firstname, p.lastname, r.name as resolution,
 * count(*) as count,
 * avg(time) as seconds
 */
use Blossom\Classes\View;

include APPLICATION_HOME.'/blocks/html/reports/parameters.inc';
$h = $this->template->getHelper('formatDuration');
?>
<div class="searchResultsRows">
	<h2>Categories Report</h2>
	<?php
		foreach ($this->data as $cid=>$c) {
			$category = View::escape($c['name']);
			$open = 0;
			$closed = 0;
			echo "
			<div><h3>$category</h3>
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
				foreach ($c['people'] as $pid=>$p) {
					$counts = array('open'=>0, 'closed'=>0, 'substatus'=>array());
					foreach ($p['data'] as $r) {
						if ($r['substatus']) {
							$counts['substatus'][] = "$r[substatus]: $r[count]";
						}
						$counts[$r['status']] += $r['count'];
						if (in_array($r['status'], array('open','closed'))) {
							$$r['status'] += $r['count'];
						}
					}
					$substatus = $counts['substatus']
						? implode(', ', $counts['substatus'])
						: '';
					$person = View::escape($p['name']);
					$total = $counts['open'] + $counts['closed'];
					echo "
					<tr><th>$person</th>
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
