<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Report;

$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts',BASE_URI.'/js/reports/slaCharts.js');
$SLA_DATA = array('openTicketCounts'=>array(), 'slaPercentages'=>array());

include APPLICATION_HOME.'/blocks/html/reports/parameters.inc';
$start = !empty($_REQUEST['enteredDate']['start'])
	? strtotime($_REQUEST['enteredDate']['start'])
	: strtotime('-1 month');
$end =   !empty($_REQUEST['enteredDate']['end'])
	? strtotime($_REQUEST['enteredDate']['end'])
	: time();
?>
<style>
	.chart { width:100%; height:200px; }
</style>
<div id="slaReport">
	<h2>SLA</h2>
	<div class="chartData">
		<h3>Outstanding Open Cases</h3>
		<div id="openTicketsChart" class="chart"></div>

		<h3>SLA Percentages</h3>
		<div id="slaChart" class="chart"></div>

		<table>
			<thead>
				<tr><th>Date</th>
					<th>Open Cases</th>
					<th>SLA %</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$dates = Report::generateDateArray ($start, $end);
				foreach ($dates as $date) {
					$open = Report::outstandingTicketCount    ($date, $_REQUEST);
					$sla  = Report::closedTicketsSlaPercentage($date, $_REQUEST);
					echo "
					<tr><td>$date</td>
						<td>$open</td>
						<td>$sla</td>
					</tr>
					";
					$SLA_DATA['openTicketCounts'][] = array(
						'date'=>$date, 'open'=>$open
					);
					$SLA_DATA['slaPercentages'][] = array(
						'date'=>$date, 'sla%'=>$sla
					);
				}
			?>
			</body>
		</table>
	</div>
</div>
<script>
var SLA_DATA = <?php echo json_encode($SLA_DATA); ?>
</script>
