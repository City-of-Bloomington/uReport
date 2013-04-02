"use strict";
/**
 * Opens a popup window letting the user search for and choose a person
 *
 * To use this script the HTML elements must have the correct IDs so
 * we can update those elements when the callback is triggered.
 * You then register the PERSON_CHOOSER.open function as the onclick handler,
 * passing in the fieldname you are using for your inputs elements.
 *
 * Here is the minimal HTML required:
 * <input id="{$fieldname}_id" value="" />
 * <span  id="{$fieldname}-name"></span>
 * <a onclick=\"PERSON_CHOOSER.open('$fieldname');\">Change Person</a>
 *
 * Example as it would appear in the final HTML:
 * <input id="reportedByPerson_id" value="" />
 * <span  id="reportedByPerson-name"></span>
 * <a onclick=\"PERSON_CHOOSER.open('reportedByPerson');\">Change Person</a>
 *
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
var PERSON_CHOOSER = {
	fieldname: '',
	popup: {},
	open: function (fieldname) {
		PERSON_CHOOSER.fieldname = fieldname;
		PERSON_CHOOSER.popup = window.open(
			CRM.BASE_URL + '/people?popup=1;callback=PERSON_CHOOSER.setPerson',
			'popup',
			'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
		);
	},
	setPerson: function (person_id) {
		YUI().use('node', 'io', 'json-parse', function (Y) {
			Y.io(CRM.BASE_URL + '/people/view?format=json;person_id=' + person_id, {
				on: {
					complete: function (id, o, args) {
						var person = Y.JSON.parse(o.responseText);
						var idElement   = '#' + PERSON_CHOOSER.fieldname + '_id';
						var nameElement = '#' + PERSON_CHOOSER.fieldname + '-name';
						Y.one(idElement).set('value', person.id);
						Y.one(nameElement).setContent(person.fullname);
						PERSON_CHOOSER.popup.close();
					}
				}
			});
		});
	}
}
