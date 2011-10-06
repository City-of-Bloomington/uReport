"use strict";
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
var CLIENT = {
	type: 'development',
	baseURL: 'https://bloomington.in.gov/ureport/open311/',
	endpoint: '',
	service_name:'',
	/**
	 * Read what open311 server we want to point to and load it's discovery
	 * We're most likely going to pass in the server via a parameter in the URL
	 */
	init: function () {
		// Use a regular expression to grab the parameter out of the url
		/*
		var r = /open311Server=(.+)/;
		var matches = r.exec(document.location.search);
		if (matches && matches[1]) {
			CLIENT.baseURL = matches[1];
			CLIENT.getDiscovery();
		}
		*/
		if(CLIENT.baseURL){
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
			CLIENT.getServiceList();
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
		var html = '<form method="get"><fieldset><label>Choose a service<select name="service_code" id="service_code" onchange="CLIENT.getServiceDefinition(this.options[this.selectedIndex].value);service_name=this.options[this.selectedIndex].text;">';
		for (var i in r) {
			html += '<option value="' + r[i].service_code + '">' + r[i].service_name + '</option>';
		}
		html += '</select></label></fieldset></form>';
		document.getElementById('mainContent').innerHTML = html;
 	},
	getServiceDefinition: function (service_code) {
		
		YUI().use('jsonp',function(Y) {
			var url = CLIENT.endpoint + '/services/' + service_code + '.json?callback={callback}';
			Y.jsonp(
				url,
				CLIENT.handleServiceDefinitionResponse
			);
		})
	},
	/**
	 * Update the screen with the form for this service
	 * this function will render the form
	 */
	handleServiceDefinitionResponse: function (r) {
		var html = '<h2>'+service_name+' Report</h2>'+
			//'<form onsubmit="postServiceRequest()" method="post">'+
			'<form action="'+CLIENT.endpoint+'/requests/" method="post">'+
			'	<fieldset>'+				
			'		<input type="hidden" name="service_code" value="'+r.service_code+'" />'+
			'		<input type="hidden" name="jurisdiction_id" value="bloomington.in.gov" />'+		
			'		<table>'+
			'			<tr>'+
			'				<td><label id="first_name">First Name</label></td>'+
			'				<td><input name="first_name" id="first_name" value="" /></td>'+
			'			</tr>'+
			'			<tr>'+
			'				<td><label id="last_name">Last Name</label></td>'+
			'				<td><input name="last_name" id="last_name" value="" /></td>'+
			'			</tr>'+								
			'			<tr>'+
			'				<td><label id="phone">Phone</label></td>'+
			'				<td><input name="phone" id="phone" value="" /></td>'+
			'			</tr>'+
			'			<tr>'+
			'				<td><label id="email">Email</label></td>'+
			'				<td><input name="email" id="email" value="" /></td>'+
			'			</tr>'+
			'			<tr>'+
			'				<td><label id="address_string">Address</label></td>'+
			'				<td><input name="address_string" id="address_string" value="" /></td>'+
			'			</tr>'+
			'			<tr>'+
			'				<td><label id="address_id">Address ID</label></td>'+
			'				<td><input name="address_id" id="address_id" value="" /></td>'+
			'			</tr>'+
			'			<tr>'+
			'				<td><label id="lat">Latitude</label></td>'+
			'				<td><input name="lat" id="lat" value="" /></td>'+
			'			</tr>'+
			'			<tr>'+
			'				<td><label id="long">Longitude</label></td>'+
			'				<td><input name="long" id="long" value="" /></td>'+
			'			</tr>'+
			'			<tr>'+
			'				<td><label id="description">Description</label></td>'+
			'				<td><textarea name="notes" id="description"></textarea>'+
			'				</td>'+
			'			</tr>';
		for (var i in r.attributes) {
			var name = r.attributes[i].code;
			var description = r.attributes[i].description;
			html += '			<tr>'+
					'				<td><label id="'+name+'">'+description+'</label>'+
					'			</td>'+
					'			<td>';
			if(r.attributes[i].datatype){
				var type = r.attributes[i].datatype;
				switch(type){
					case 'singlevaluelist':
						html += '<select name="'+name+'" id="'+name+'">';
						for(var j in r.attributes[i].values){
							var key = r.attributes[i].values[j].key;
							var value= r.attributes[i].values[j].name;
							html += '				<option value="'+key+'">'+value+'</optoin>';						
						}
						html += '			</select>';
						break;
					case 'multivaluelist':
						html += '			<select multiple="multiple" name="'+name+'" id="'+name+'">';
						for(j in r.attributes[i].values){
							var key = r.attributes[i].values[j].key;
							var value = r.attributes[i].values[j].name;
							html += '				<option value="'+key+'">'+value+'</optoin>';						
						}
						html += '			</select>';
						break;
					case 'text':
						html += '			<textarea name="'+name+'" id="'+name+'"></textarea>';
						break;
					case 'string':
					case 'number':
					case 'datetime':
					default:	
						html += '			<input id="'+name+'" name="'+name+'" />';
				}
			}
			else{
				html += '			<input id="'+name+'" name="'+name+'" />';
			}
			html += '			</td></tr>';
		}
		html += '		</table>'+
		'		<input type="submit" value="Submit" />'+
		'	</fieldset>'+
		'</form>';
		//
		document.getElementById('mainContent').innerHTML = html;
		
	},
	/**
	 * Send in the post from the form
	 */
	postServiceRequest: function () {
	},
}
window.addEventListener('load', CLIENT.init, false);