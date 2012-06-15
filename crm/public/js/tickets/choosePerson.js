"use strict";
/**
 * Opens a new window for the user to lookup/add a person
 *
 * When the user finally selects or adds a person, the HTML is supposed
 * to call the callback function, PERSON_CHOOSER.setPerson().
 *
 * Every HTML block involved needs to pass along the callback parameter.
 * Any link or action that can be considered selecting a person should
 * use the callback function, instead of it's normal href.
 * People Blocks: searchForm, searchResults, personList, updatePersonForm
 *
 * PeopleController actions need to replace header redirections with
 * a redirection to an empty page with javascript for the callback function.
 * PeopleController::update
 *
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
var PERSON_CHOOSER = {
	popup: {},
	setPerson: function (person_id) {
		YUI().use('node', 'io', function (Y) {
			Y.one('#reportedByPerson_id').set('value', person_id);

			var bd = Y.one('#reportedByPersonChooser .bd');
			bd.setContent('<img src="' + CRM.BASE_URL + '/skins/local/images/busy.gif" />');

			Y.io(CRM.BASE_URL + '/people/view?partial=people/personInfo.inc,Reported Cases;disableButtons=1;person_id=' + person_id, {
				on: {
					complete: function (id, o, args) {
						bd.setContent(o.responseText);
						PERSON_CHOOSER.popup.close();
					}
				}
			});
		});
	}
};
YUI().use('node', function (Y) {
	Y.on('click', function (e) {
		PERSON_CHOOSER.popup = window.open(
			CRM.BASE_URL + '/people?popup=1;callback=PERSON_CHOOSER.setPerson',
			'popup',
			'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
		);
		e.preventDefault();
	}, '#findPersonButton');
});
