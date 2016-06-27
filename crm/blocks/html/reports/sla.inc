<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Report;

$this->template->addToAsset('scripts', GOOGLE_LOADER);
$this->template->addToAsset('scripts', BASE_URI.'/js/reports/slaCharts.js');

$SLA_DATA = [
    'openTicketCounts' => [
        'cols'=>[
            ['label'=>'Date',  'type'=>'date'],
            ['label'=>'Count', 'type'=>'number']
        ],
        'rows' => []
    ],
    'slaPercentages'   => [
        'cols'=>[
            ['label'=>'Date',    'type'=>'date'],
            ['label'=>'Percent', 'type'=>'number']
        ],
        'rows' => []
    ]
];

$this->_include('reports/parameters.inc');
$start = !empty($_REQUEST['enteredDate']['start'])
	? strtotime($_REQUEST['enteredDate']['start'])
	: strtotime('-1 month');
$end =   !empty($_REQUEST['enteredDate']['end'])
	? strtotime($_REQUEST['enteredDate']['end'])
	: time();
?>
<style type="text/css">
	.chart { width:100%; height:250px; }
	.charts-tooltip div { color:infotext; }
</style>
<div id="slaReport">
	<h2>SLA</h2>
	<div class="chartData">
		<h3>Outstanding Open Cases</h3>
		<div id="openTicketCounts" class="chart"></div>

		<h3>SLA Percentages</h3>
		<div id="slaPercentages" class="chart"></div>

        <script type="text/javascript">
            <?php
                $dates = Report::generateDateArray($start, $end);

                $o = [];
                $s = [];
                foreach ($dates as $date) {
                    $open = Report::outstandingTicketCount($date, $_REQUEST);
                    $sla  = Report::closedTicketsSlaPercentage($date, $_REQUEST);

                    if (empty($open)) { $open = 0; }
                    if (empty($sla )) { $sla  = 0; }

                    $d = getdate(strtotime($date));
                    $m = $d['mon'] - 1; // Javascript months are zero-based

                    $o[] = "{c:[{v: new Date($d[year], $m, $d[mday])}, {v: $open}]}";
                    $s[] = "{c:[{v: new Date($d[year], $m, $d[mday])}, {v: $sla}]}";
                }
            ?>
            var SLA_DATA = {
                openTicketCounts: {
                    cols: [
                        {label: 'Date',  type: 'date'},
                        {label: 'Count', type: 'number'}
                    ],
                    rows: [<?= implode(",\n", $o); ?>]
                },
                slaPercentages: {
                    cols: [
                        {label: 'Date',    type: 'date'},
                        {label: 'Percent', type: 'number'}
                    ],
                    rows: [<?= implode(",\n", $s); ?>]
                }
            };
        </script>
	</div>
</div>
