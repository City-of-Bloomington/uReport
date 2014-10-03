"use strict";
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
google.load('visualization', '1.0', {'packages': ['corechart', 'table']});
google.setOnLoadCallback(function () {
    var line = function (id, data) {
        var datatable = new google.visualization.DataTable(data),
            chart     = new google.visualization.LineChart(document.getElementById(id));

        if (data.rows.length > 1) {
            chart.draw(datatable, {
                hAxis:  { format: 'MMM d, y', slantedText: true },
                legend: { position: 'none'}
            });
        }
    }
    line('openTicketCounts', SLA_DATA.openTicketCounts);
    line('slaPercentages',   SLA_DATA.slaPercentages);
});
