"use strict";
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
var CLIENT = {
	type: 'development',
	baseURL: '',
	endpoint: '',
	/**
	 * Read what open311 server we want to point to and load it's discovery
	 * We're most likely going to pass in the server via a parameter in the URL
	 */
	init: function () {
		// Use a regular expression to grab the parameter out of the url
		var r = /open311Server=(.+)/;
		var matches = r.exec(document.location.search);
		if (matches && matches[1]) {
			CLIENT.baseURL = matches[1];
			CLIENT.getDiscovery();
		}
		else {
			alert('No open311 server defined');
		}
	},
	getDiscovery: function () {
		YUI().use('jsonp', function (Y) {
			Y.jsonp(CLIENT.baseURL + '/discovery.json?callback={callback}',CLIENT.handleDiscoveryResponse);
		});
	},
	/**
	 * Go through all the endpoints and pick out one for v2 of the spec
	 */
	handleDiscoveryResponse: function (r) {
		CLIENT.endpoint = '';
		for (var i in r.endpoints) {
			if (r.endpoints[i].specification == 'http://wiki.open311.org/GeoReport_v2' &&
				r.endpoints[i].type == CLIENT.type) {
				CLIENT.endpoint = r.endpoints[i].url;
			}
		}
		if (CLIENT.endpoint) {
			CLIENT.getServices();
		}
		else {
			alert('Production endpoint not found');
		}
	},
	getServiceList: function () {
		YUI().use('jsonp', function (Y) {
			Y.jsonp(
				CLIENT.endpoint + '/services.json?callback={callback}',
				CLIENT.handleServiceListResponse
			);
		});
	},
	/**
	 * Update the screen with a drop down for the user to choose a service
	 */
	handleServiceListResponse: function (r) {
		var html = '<form method="get"><fieldset><label>Choose a service<select name="service_code" id="service_code" onchange="CLIENT.getService(this.options[this.selectedIndex].value)">';
		for (var i in r) {
			html += '<option value="' + r[i].service_code + '">' + r[i].service_name + '</option>';
		}
		html += '</select></label></fieldset></form>';
		document.getElementById('mainContent').innerHTML = html;
 	},
	getServiceDefinition: function (service_code) {
		YUI().use('jsonp',function(Y) {
			Y.jsonp(
				CLIENT.endpoint + '/services/' + service_code + '.json?callback={callback}',
				CLIENT.handleServiceDefinitionResponse
			);
		}
	},
	/**
	 * Update the screen with the form for this service
	 */
	handleServiceDefintionResponse: function (r) {
		var html = '';
		document.getElementById('mainContent').innerHTML = html;
	},
	/**
	 * Send in the post from the form
	 */
	postServiceRequest: function () {
	},
}
window.addEventListener('load', CLIENT.init, false);