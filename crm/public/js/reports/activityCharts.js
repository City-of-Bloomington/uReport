"use strict";
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
google.load('visualization', '1.0', {'packages': ['corechart', 'table']});
google.setOnLoadCallback(function () {
    var pie = function (id, data) {
            var chart = new google.visualization.PieChart(document.getElementById(id + '_chart'));

            if (data.length > 1) {
                chart.draw(google.visualization.arrayToDataTable(data, false), {
                    legend: { position:'none' },
                    pieSliceText: 'none'
                });
            }
        },
        line = function (id, data) {
            var chart = new google.visualization.LineChart(document.getElementById(id + '_chart'));

            if (data.length > 1) {
                chart.draw(google.visualization.arrayToDataTable(data, false));
            }
        },
        table = function (id, data) {
            var chart = new google.visualization.Table(document.getElementById(id + '_chart'));
            if (data.length > 1) {
                chart.draw(google.visualization.arrayToDataTable(data, false));
            }
        };
    pie('currentOpenTickets', ACTIVITY_DATA.currentOpenTickets);
    pie('openedTickets',      ACTIVITY_DATA.openedTickets);
    pie('closedTickets',      ACTIVITY_DATA.closedTickets);

    line('activityThisMonth', ACTIVITY_DATA.activityThisMonth);

    table('categoryActivity', ACTIVITY_DATA.categoryActivity);
});
