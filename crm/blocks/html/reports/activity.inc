<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Report;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\View;

$h = $this->template->getHelper('formatDuration');
$this->template->addToAsset('scripts', JQUERY.'/jquery.min.js');
$this->template->addToAsset('scripts', GOOGLE_LOADER);
$this->template->addToAsset('scripts', BASE_URI.'/js/reports/activityCharts.js');

$ACTIVITY_DATA = [
    'currentOpenTickets' => [['Category', 'Count']],
    'openedTickets'      => [['Category', 'Count']],
    'closedTickets'      => [['Category', 'Count']],
	'activityThisMonth'  => [['Date', 'Opened', 'Closed']],
	'categoryActivity'   => [[
        'Category', 'SLA','Avg Days Open', 'Open',
        'Opened Today', 'Opened This Week', 'Opened This Month',
        'Closed Today', 'Closed This Week', 'Closed This Month'
	]]
];
?>
<style type="text/css">
	.chart { width:100%; height:200px; }
	#pieCharts { overflow:auto; }

	#pieCharts .chartData { float:left; width: 200px; height:240px; }
	#activityThisWeek { width:600px; clear:both; }
	#categoryActivity .original { display:none; }
</style>
<div id="activityReport">
	<h2>Activity Report</h2>
	<div id="pieCharts">
		<div id="currentOpenTickets" class="chartData">
			<h3>Tickets currently open</h3>
			<div class="chart" id="currentOpenTickets_chart"></div>
			<?php
                $data = Report::currentOpenTickets();
                foreach ($data as $r) {
                    $name = View::escape($r['category']);
                    $ACTIVITY_DATA['currentOpenTickets'][] = [$name, (int)$r['count']];
                }
            ?>
		</div>
		<div id="openedTickets" class="chartData">
			<h3>Tickets Opened Today</h3>
			<div class="chart" id="openedTickets_chart"></div>
			<?php
                $data = Report::openedTickets();
                foreach ($data as $r) {
                    $name = View::escape($r['category']);
                    $ACTIVITY_DATA['openedTickets'][] = [$name, (int)$r['count']];
                }
            ?>
		</div>
		<div id="closedTickets" class="chartData">
			<h3>Tickets closed today</h3>
			<div class="chart" id="closedTickets_chart"></div>
            <?php
                $data = Report::closedTickets();
                foreach ($data as $r) {
                    $name = View::escape($r['category']);
                    $ACTIVITY_DATA['closedTickets'][] = [$name, (int)$r['count']];
                }
            ?>
		</div>
	</div>
	<div id="activityThisMonth" class="chartData">
		<h3>Activity the past month</h3>
		<div class="chart" id="activityThisMonth_chart"></div>
        <?php
            $d = [];
            $start = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime('-1 month'));
            $end   = date(ActiveRecord::MYSQL_DATE_FORMAT);

            $data = Report::openTicketsCount($start, $end);
            foreach ($data as $r) {
                $d[$r['date']]['open'] = $r['open'];
            }

            $data = Report::closedTicketsCount($start, $end);
            foreach ($data as $r) {
                $d[$r['date']]['closed'] = $r['closed'];
            }

            foreach ($d as $date=>$t) {
                $ACTIVITY_DATA['activityThisMonth'][] = [
                    $date,
                    isset($t['open'])   ? (int)$t['open']   : 0,
                    isset($t['closed']) ? (int)$t['closed'] : 0
                ];
            }
        ?>
	</div>
</div>
<div id="categoryActivity" class="yui3-skin-sam">
	<p>* Average Days spent open is calculated as how long tickets stayed open during the entire
		history of the data.
	</p>
	<div id="categoryActivity_chart"></div>
    <?php
        $data = Report::categoryActivity();
        foreach ($data as $r) {
            $ACTIVITY_DATA['categoryActivity'][] = [
                $r['name'],
                (int)$r['slaDays'],
                (int)$r['days'],
                (int)$r['currentopen'],
                (int)$r['openedday'],
                (int)$r['openedweek'],
                (int)$r['openedmonth'],
                (int)$r['closedday'],
                (int)$r['closedweek'],
                (int)$r['closedmonth']
            ];
        }
    ?>
</div>
<script type="text/javascript">
var ACTIVITY_DATA = <?php echo json_encode($ACTIVITY_DATA, JSON_PRETTY_PRINT); ?>
</script>
