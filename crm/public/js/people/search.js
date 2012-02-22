"use strict";
YUI().use('autocomplete', function (Y) {
	Y.one('#firstname').plug(Y.Plugin.AutoComplete, {
		source: CRM.BASE_URL + '/people/distinct.php?format=json;field=firstname;query={query}'
	});
	Y.one('#lastname').plug(Y.Plugin.AutoComplete, {
		source: CRM.BASE_URL + '/people/distinct.php?format=json;field=lastname;query={query}'
	});
	Y.one('#email').plug(Y.Plugin.AutoComplete, {
		source: CRM.BASE_URL + '/people/distinct.php?format=json;field=email;query={query}'
	});
	Y.one('#organization').plug(Y.Plugin.AutoComplete, {
		source: CRM.BASE_URL + '/people/distinct.php?format=json;field=organization;query={query}'
	});
});
