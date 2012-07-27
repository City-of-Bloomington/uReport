<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$h = $this->template->getHelper('formatDuration');
$this->template->addToAsset('scripts',YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts',BASE_URI.'/js/reports/activityCharts.js');
$ACTIVITY_DATA = array(
	'currentOpenTickets'=>array(),
	'ticketsClosedToday'=>array(),
	'activityThisWeek'  =>array()
);
?>
<style type="text/css">
	.chartData table { display:none; }
	.chart { width:100%; height:200px; }
	#pieCharts { overflow:auto; }

	#pieCharts .chartData { float:left; height:240px; width:200px; }
	#activityThisWeek { width:600px; clear:both; }
	#categoryActivity .original { display:none; }
</style>
<div id="activityReport">
	<h2>Activity Report</h2>
	<div id="pieCharts">
		<div id="currentOpenTickets" class="chartData">
			<h3>Tickets currently open</h3>
			<div class="chart"></div>
			<table>
				<thead>
					<tr><th>Category</th><th>Open</th></tr>
				</thead>
				<tbody>
				<?php
					$data = Report::currentOpenTickets();
					foreach ($data as $r) {
						$name = View::escape($r['category']);
						echo "
						<tr><td>$name</td>
							<td>$r[open]</td>
						</tr>
						";
						$ACTIVITY_DATA['currentOpenTickets'][] = array('category'=>$name, 'open'=>$r['open']);
					}
				?>
				</tbody>
			</table>
		</div>
		<div id="ticketsOpenedToday" class="chartData">
			<h3>Tickets Opened Today</h3>
			<div class="chart"></div>
			<table>
				<thead>
					<tr><th>Category</th><th>Open</th></tr>
				</thead>
				<tbody>
				<?php
					$data = Report::openedTickets();
					foreach ($data as $r) {
						$name = View::escape($r['category']);
						echo "
						<tr><td>$name</td>
							<td>$r[open]</td>
						</tr>
						";
						$ACTIVITY_DATA['ticketsOpenedToday'][] = array('category'=>$name, 'open'=>$r['open']);
					}
				?>
				</tbody>
			</table>
		</div>
		<div id="ticketsClosedToday" class="chartData">
			<h3>Tickets closed today</h3>
			<div class="chart"></div>
			<table>
				<thead>
					<tr><th>Category</th><th>Closed</th></tr>
				</thead>
				<tbody>
				<?php
					$data = Report::closedTickets();
					foreach ($data as $r) {
						$name = View::escape($r['category']);
						echo "
						<tr><td>$name</td>
							<td>$r[closed]</td>
						</tr>
						";
						$ACTIVITY_DATA['ticketsClosedToday'][] = array('category'=>$name, 'closed'=>$r['closed']);
					}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<div id="activityThisWeek" class="chartData">
		<h3>Activity the past two weeks</h3>
		<div class="chart"></div>
		<table>
			<thead>
				<tr><th>Date</th><th>Open</th></tr>
			</thead>
			<tbody>
			<?php
				$d = array();
				$start = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime('-2 week'));
				$end   = date(ActiveRecord::MYSQL_DATE_FORMAT);
				$data = Report::openTicketsCount($start, $end);
				foreach ($data as $r) {
					echo "
					<tr><td>$r[date]</td>
						<td>$r[open]</td>
					</tr>
					";

					$d[$r['date']]['open'] = $r['open'];
				}
			?>
			</tbody>
		</table>
		<table>
			<thead>
				<tr><th>Date</th><th>Closed</th></tr>
			</thead>
			<tbody>
			<?php
				$start = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime('-1 week'));
				$end   = date(ActiveRecord::MYSQL_DATE_FORMAT);
				$data = Report::closedTicketsCount($start, $end);
				foreach ($data as $r) {
					echo "
					<tr><td>$r[date]</td>
						<td>$r[closed]</td>
					</tr>
					";
					$d[$r['date']]['closed'] = $r['closed'];
				}
				foreach ($d as $date=>$t) {
					$ACTIVITY_DATA['activityThisWeek'][] = array(
						'category'   =>$date,
						'opened' =>isset($t['open'])   ? $t['open']   : 0,
						'closed' =>isset($t['closed']) ? $t['closed'] : 0
					);
				}
			?>
			</tbody>
		</table>
	</div>
</div>
<div id="categoryActivity" class="yui3-skin-sam">
	<div class="datatable"></div>
	<table class="original">
		<thead>
			<tr><th></th>
				<th>Open</th>
				<th colspan="3">Opened</th>
				<th colspan="3">Closed</th>
			</tr>
			<tr><th></th>
				<th>now</th>
				<th>day</th>
				<th>week</th>
				<th>month</th>
				<th>day</th>
				<th>week</th>
				<th>month</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$data = Report::categoryActivity();
			foreach ($data as $r) {
				$name = View::escape($r['name']);
				$fields = array(
					'currentopen',
					'openedday', 'openedweek', 'openedmonth',
					'closedday', 'closedweek', 'closedmonth'
				);
				foreach ($fields as $f) {
					$$f = $r[$f] ? $r[$f] : 0;
				}
				echo "
				<tr><th>$name</th>
					<td>$currentopen</td>
					<td>$openedday</td>
					<td>$openedweek</td>
					<td>$openedmonth</td>
					<td>$closedday</td>
					<td>$closedweek</td>
					<td>$closedmonth</td>
				</tr>
				";
				foreach ($r as $i=>$v) {
					if ($i != 'name') { $r[$i] = (int)$v; }
				}
				$ACTIVITY_DATA['categoryActivity'][] = $r;
			}
		?>
		</tbody>
	</table>
</div>
<script type="text/javascript">
var ACTIVITY_DATA = <?php echo json_encode($ACTIVITY_DATA); ?>
</script>
