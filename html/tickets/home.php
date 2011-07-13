<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$template = new Template('search');
$template->blocks['search-form'][] = new Block('tickets/searchForm.inc');

// Map any extra AddressService fields to the Ticket search fields
$fields = TicketList::getDisplayableFields();
foreach (AddressService::$customFieldDescriptions as $key=>$value) {
	$fields[$key] = array('displayName'=>$value['description'],'searchOn'=>$key,'sortOn'=>$key);
}

// Logged in users should have a default search
// Search for open tickets assigned to them
if (!count(array_intersect(array_keys($fields),array_keys($_GET)))
	&& isset($_SESSION['USER'])) {
	$_GET['status'] = 'open';
	$_GET['assignedPerson'] = array("{$_SESSION['USER']->getId()}");
}

// Build the search query
if (count(array_intersect(array_keys($fields),array_keys($_GET)))) {
	$search = array();
	foreach ($fields as $field=>$definition) {
		if (isset($_GET[$field])) {
			$value = is_string($_GET[$field]) ? trim($_GET[$field]) : $_GET[$field];
			if ($value) {
				$search[$definition['searchOn']] = $value;
			}
		}
	}

	if (count($search)) {
		// Create the report
		$report = (isset($_GET['report']) && $_GET['report']
					&& is_file(APPLICATION_HOME."/blocks/html/tickets/reports/$_GET[report].inc"))
			? new Block("tickets/reports/$_GET[report].inc")
			: new Block('tickets/searchResults.inc');
		$report->search = $search;

		// If the user asked for a sorting, see if we can accomodate them
		// What we can sort by is defined in $fields
		// See TicketList::getDisplayableFields()
		if (isset($_GET['sort'])) {
			$keys = array_keys($_GET['sort']);
			$fieldToSortBy = $keys[0];
			$value = $_GET['sort'][$fieldToSortBy];

			if (array_key_exists($fieldToSortBy,$fields)) {
				$report->sort = array($fields[$fieldToSortBy]['sortOn']=>(int)$value);
			}
		}

		// Tell the report what fields we want displayed
		$fieldsToDisplay = array();
		$f = isset($_GET['fields']) ? $_GET['fields'] : TicketList::getDefaultFieldsToDisplay();
		foreach (array_keys($f) as $field) {
			if (isset($fields[$field])) {
				$fieldsToDisplay[$field] = $fields[$field];
			}
		}
		$report->fieldsToDisplay = $fieldsToDisplay;


		$template->blocks['search-results'][] = new Block('tickets/customReportLinks.inc');
		$template->blocks['search-results'][] = $report;
	}
}

echo $template->render();
