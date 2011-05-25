<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET return_url (optional)
 */
$template = isset($_GET['format'])
	? new Template('default',$_GET['format'])
	: new Template('locations');

if ($template->outputFormat=='html') {
	$findForm = new Block('locations/findLocationForm.inc');
	if (isset($_GET['return_url'])) {
		$findForm->return_url = $_GET['return_url'];
	}
	$template->blocks['location-panel'][] = $findForm;
}

if (isset($_GET['location'])) {
	$results = new Block(
		'locations/findLocationResults.inc',
		array(
			'results'=>Location::search($_GET['location'])
		)
	);
	if (isset($_GET['return_url'])) {
		$results->return_url = $_GET['return_url'];
	}

	if ($template->outputFormat=='html') {
		$template->blocks['location-panel'][] = $results;
	}
	else {
		$template->blocks[] = $results;
	}
}
echo $template->render();