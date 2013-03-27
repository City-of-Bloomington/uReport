"use strict";
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
YUI().use('node', 'charts', 'charts-legend', 'stylesheet', function (Y) {
	var openTicketsChart = new Y.Chart({
		axes: {
			category: {
				keys: ["date"],
				type: "category",
				styles: { label: { rotation: -45 } }
			},
			values: { roundingMethod: 'auto'}
		},
		categoryKey: "date",
		dataProvider:SLA_DATA.openTicketCounts,
		horizontalGridlines: true,
		verticalGridlines: true,
		render:"#openTicketsChart"
	});
	var slaChart = new Y.Chart({
		axes: {
			category: {
				keys: ["date"],
				type: "category",
				styles: { label: { rotation: -45 } }
			},
			values: { roundingMethod: 'auto'}
		},
		categoryKey: "date",
		dataProvider:SLA_DATA.slaPercentages,
		horizontalGridlines: true,
		verticalGridlines: true,
		render:"#slaChart"
	});
});
