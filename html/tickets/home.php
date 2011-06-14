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

			// Try and use anthing set up in $fields first.
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
		if (isset($sort)) {
			$ticketList = new TicketList();
			$ticketList->find($search,$sort);
		}
		else {
			$ticketList = new TicketList($search);
		}

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
		$paginator = $ticketList->getPaginator(50,$page);

		$template->blocks['search-results'][] = new Block(
			'tickets/searchResults.inc',
			array(
				'ticketList'=>$paginator,
				'title'=>'Search Results',
				'fields'=>isset($_GET['fields']) ? $_GET['fields'] : null
			)
		);
		$template->blocks['search-results'][] = new Block(
			'pageNavigation.inc',array('paginator'=>$paginator)
		);

	}
}

echo $template->render();
