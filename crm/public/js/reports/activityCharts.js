"use strict";
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
YUI().use('node', 'charts', 'charts-legend', 'datatable', 'stylesheet', function (Y) {
	var activityThisWeek = new Y.Chart({
		legend: {
			position: "right"
		},
		axes: {
			category: {
				keys: ["category"],
				type: "category",
				styles: { label: { rotation: -45 } }
			},
			values: {roundingMethod: 'auto'}
		},
		categoryKey: "category",
		dataProvider:ACTIVITY_DATA.activityThisWeek,
		horizontalGridlines: true,
		verticalGridlines: true,
		render:"#activityThisWeek .chart"
	});
	var categoryActivity = new Y.DataTable({
		columns: [
			{ key: 'name',    label: 'Category', sortable:true },
			{ key: 'slaDays', label: 'SLA',      sortable:true },
			{ label: 'Open',      children:[{ key: 'currentopen', label: 'now',  sortable:true }]},
			{ label: 'Avg Days*', children:[{ key: 'days',        label: 'open', sortable:true }]},
			{ label: 'Opened in the last...', children: [
				{ key:'openedday',   label:'day',   sortable:true  },
				{ key:'openedweek',  label:'week',  sortable:true  },
				{ key:'openedmonth', label:'month', sortable:true  }
			]},
			{ label: 'Closed in the last...', children: [
				{ key:'closedday',   label:'day',   sortable:true  },
				{ key:'closedweek',  label:'week',  sortable:true  },
				{ key:'closedmonth', label:'month', sortable:true  }
			]}
		],
		data: ACTIVITY_DATA.categoryActivity,
	}).render('#categoryActivity .datatable');
	function pie (id, data) {
		var div = '#' + id + ' .chart';
		if (data && data.length) {
			new Y.Chart({
				dataProvider: data,
				render: div,
				type: 'pie'
			});
		}
		else {
			Y.one(div).setHTML('no tickets found');
		}
	}
	pie('currentOpenTickets', ACTIVITY_DATA.currentOpenTickets);
	pie('ticketsOpenedToday', ACTIVITY_DATA.ticketsOpenedToday);
	pie('ticketsClosedToday', ACTIVITY_DATA.ticketsClosedToday);
	var style = new Y.StyleSheet(".chartData table { display:none; }");
});