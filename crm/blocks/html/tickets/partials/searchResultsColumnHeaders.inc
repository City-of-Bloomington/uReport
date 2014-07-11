<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Apache_Solr_Response $this->solrObject
 */
use Application\Models\Person;
use Application\Models\Search;
use Application\Models\TicketTable;
use Blossom\Classes\Url;
echo "
<table>
<thead>
	<tr><td>{$this->translate('labels.sort_by')}:</td>
";
	$c = $this->solrObject->response->numFound;
	if ($c) {
		$currentSort = Search::$defaultSort;
		if (isset($_GET['sort'])) {
			$keys = array_keys($_GET['sort']);
			$currentSort['field'] = $keys[0];
			$currentSort['order'] = ($_GET['sort'][$keys[0]] == 'asc')
				? 'asc'
				: 'desc';
		}

		$url = new Url($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		$displayableFields = TicketTable::getDisplayableFields();
		$sortableFields = Search::$sortableFields['ticket'];

		// Loop through all the fields the user asked to see
		// The key will be the fieldname and the value will be "On" or 1
		// or whatever the browser gives to checkboxes when you post a form.
		// We'll loop through all of the fieldnames that were checked
		if (!isset($_GET['fields'])) {
			$_GET['fields'] = TicketTable::$defaultFieldsToDisplay;
		}
		foreach ($_GET['fields'] as $name => $checked) {
			// Make sure they're allowed to see this field
			if (array_key_exists($name, $displayableFields)
				&& (false === strpos($name, 'Person') || Person::isAllowed('people'))) {

				$label = $displayableFields[$name];

				// _id fields are sorted using fields without _id in the fieldname
				if (substr($name, -3) == '_id') { $name = substr($name, 0, -3); }
				// If this field is being used for sorting,
				// draw a url that reverses the sort order
				if (in_array($name, $sortableFields)) {
					$url->sort = ($currentSort['field'] == $name
								  && $currentSort['order'] == 'asc')
						? array($name=>'desc')
						: array($name=>'asc');
					$icon = '';
					if ($name == $currentSort['field']) {
						$icon = $currentSort['order'] == 'desc'
							? '<span class="fa fa-angle-down"></span>'
							: '<span class="fa fa-angle-up"></span>';
					}
					echo "<th><a href=\"$url\">$label $icon</th>";
				}
			}
		}
	}
	// Remember to leave an empty cell for any buttons we might want to add in the results
echo "
	</tr>
</thead>
</table>
";
