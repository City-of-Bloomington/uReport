<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->data
 */
?>
<div><h2>Assignments Report</h2>
	<p>List people who are currently assigned tickets.</p>
<?php
	include APPLICATION_HOME.'/blocks/html/reports/parameters.inc';

function getNiceDuration($durationInSeconds) {
	$duration = '';
	$days     = floor($durationInSeconds / 86400);
	$durationInSeconds -= $days * 86400;
	$hours    = floor($durationInSeconds / 3600);
	$durationInSeconds -= $hours * 3600;
	$minutes  = floor($durationInSeconds / 60);

	if($minutes > 0) { $duration = "$minutes minutes"; }
	if($hours   > 0) { $duration = "$hours hours";     }
	if($days    > 0) { $duration = "$days days";       }
	return $duration;
}
foreach ($this->data as $id=>$o) {
	echo "
	<div>
		<h2><a href=\"".BASE_URL."/people/view?person_id=$id\">$o[firstname] $o[lastname]</a></h2>
		<table>
			<thead><tr><th>Status</th><th>Count</th><th>Avg Time Until Close</th></thead>
			<tbody>
	";
	foreach (array('open','closed') as $status) {
		if (isset($o[$status])) {
			$avg = getNiceDuration($o[$status]['seconds']);
			echo "
			<tr><th>$status</th>
				<td>{$o[$status]['count']}</td>
				<td>$avg</td>
			</tr>
			";
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