<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$template = !empty($_GET['format']) ? new Template('search',$_GET['format']) : new Template('search');
if (userIsAllowed('Tickets')) {
	$template->blocks['search-form'][] = new Block(
		'tickets/addNewForm.inc',
		array('return_url'=>new URL(BASE_URL.'/tickets/addTicket.php'))
	);
}
$template->blocks['search-form'][] = new Block('tickets/searchForm.inc');


// Logged in users should have a default search
// Search for open tickets assigned to them
#if (!TicketList::isValidSearch($_GET) && !empty($_SESSION['USER'])) {
#	$_GET['status'] = 'open';
#	$_GET['assignedPerson'] = array("{$_SESSION['USER']->getId()}");
#}

// Build the search query
if (TicketList::isValidSearch($_GET)) {
	// Create the report
	$report = (isset($_GET['report']) && $_GET['report']
				&& is_file(APPLICATION_HOME."/blocks/{$template->outputFormat}/tickets/reports/$_GET[report].inc"))
		? new Block("tickets/reports/$_GET[report].inc")
		: new Block('tickets/searchResults.inc');

	// Tell the report what fields we want displayed
	$_GET['fields'] = empty($_GET['fields'])
		? TicketList::$defaultFieldsToDisplay
		: $_GET['fields'];


	if ($template->outputFormat == 'html') {
		$template->blocks['search-results'][] = new Block('tickets/searchParameters.inc');
		$template->blocks['search-results'][] = new Block('tickets/customReportLinks.inc');
	}
	$template->blocks['search-results'][] = $report;
}

echo $template->render();
