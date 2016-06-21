<?php
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param array $this->data
 */
use Blossom\Classes\Url;

include APPLICATION_HOME.'/blocks/html/reports/parameters.inc';
$h = $this->template->getHelper('formatDuration');

?>
<div class="searchResultsRows">
	<h2>Assignments Report</h2>
	<p>List people and their assigned tickets.</p>
	<?php
        $current_url = new Url(Url::current_url());
        $search_url  = new Url(BASE_URL.'/tickets');
        $search_url->parameters = $current_url->parameters;
        unset($search_url->categories);

		foreach ($this->data as $assignedPerson_id => $p) {
			$person = self::escape($p['name']);
			$open   = 0;
			$closed = 0;
			$search_url->assignedPerson_id = $assignedPerson_id;
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
				foreach ($p['categories'] as $category_id => $c) {
					$counts = ['open'=>0, 'closed'=>0, 'substatus'=>[]];
					foreach ($c['data'] as $r) {
						if ($r['substatus']) {
							$counts['substatus'][] = "$r[substatus]: $r[count]";
						}
						$counts[$r['status']] += $r['count'];
						if (in_array($r['status'], ['open','closed'])) {
							${$r['status']} += $r['count'];
						}
					$substatus =  count($counts['substatus'])
						? implode(', ', $counts['substatus'])
						: '';
					}
					$category = self::escape($c['name']);
					$total = $counts['open'] + $counts['closed'];

					$search_url->category_id = $category_id;
					echo "
					<tr><th>$category</th>
						<td><a href=\"$search_url;status=open\">$counts[open]</a></td>
						<td><a href=\"$search_url;status=closed\">$counts[closed]</a></td>
						<td>$substatus</td>
						<td><a href=\"$search_url\">$total</a></td>
					</tr>
					";
				}
			$total = $open + $closed;
			unset($search_url->category_id);
			echo "
					<tr class=\"totals\"><td></td>
						<td><a href=\"$search_url;status=open\">$open</a></td>
						<td><a href=\"$search_url;status=closed\">$closed</a></td>
						<td></td>
						<td><a href=\"$search_url\">$total</a></td>
					</tr>
					</tbody>
				</table>
			</div>
			";
		}
	?>
</div>
