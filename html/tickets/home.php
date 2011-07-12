<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$template = new Template('search');
$template->blocks['search-form'][] = new Block('tickets/searchForm.inc');
// Map the form fields to the Ticket search fields
$fields = array(
	'enteredByPerson'=>'enteredByPerson._id',
	'assignedPerson'=>'assignedPerson._id',
	'referredPerson'=>'referredPerson._id',
	'reportedByPerson'=>'issues.reportedByPerson._id',
	'department'=>'assignedPerson.department._id',
	'city'=>'city',
	'state'=>'state',
	'zip'=>'zip',
	'type'=>'issues.type',
	'category'=>'issues.category._id',
	'contactMethod'=>'issues.contactMethod',
	'status'=>'status',
	'action'=>'history.action',
	'actionPerson'=>'history.actionPerson._id'
);
foreach (AddressService::$customFieldDescriptions as $key=>$description) {
	$fields[$key] = $key;
}

// Logged in users should have a default search
// Search for open tickets assigned to them
if (!count(array_intersect(array_keys($fields),array_keys($_GET)))
	&& isset($_SESSION['USER'])) {
	$_GET['status'] = 'open';
	$_GET['assignedPerson'] = array("{$_SESSION['USER']->getId()}");
}

if (count(array_intersect(array_keys($fields),array_keys($_GET)))) {
	$search = array();
	foreach ($fields as $field=>$key) {
		if (isset($_GET[$field])) {
			$value = is_string($_GET[$field]) ? trim($_GET[$field]) : $_GET[$field];
			if ($value) {
				$search[$key] = $value;
			}
		}
	}

	if (count($search)) {
		if (isset($_GET['sort'])) {
			$key = array_keys($_GET['sort']);
			$key = $key[0];
			$value = $_GET['sort'][$key];

			// Try and use anything set up in $fields first.
			// That's where we've defined the embedded fields in Mongo
			if (array_key_exists($key,$fields)) {
				$sort = array($fields[$key]=>(int)$value);
			}
			// Anything else that we're displaying will not be an embedded field
			// So we can just use the name of the field
			else {
				$f = TicketList::getDisplayableFields();
				if (array_key_exists($key,$f)) {
					$sort = array($key=>(int)$value);
				}
			}
		}

		$report = (isset($_GET['report']) && $_GET['report']
					&& is_file(APPLICATION_HOME."/blocks/html/tickets/reports/$_GET[report].inc"))
			? new Block("tickets/reports/$_GET[report].inc")
			: new Block('tickets/searchResults.inc');
		$report->search = $search;
		$report->fields = isset($_GET['fields']) ? $_GET['fields'] : TicketList::getDefaultFieldsToDisplay();
		if (isset($sort)) {
			$report->sort = $sort;
		}
		$template->blocks['search-results'][] = new Block('tickets/customReportLinks.inc');
		$template->blocks['search-results'][] = $report;
	}
}

echo $template->render();
