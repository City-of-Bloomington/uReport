<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$template = new Template('locations');

$template->blocks['location-panel'][] = new Block('locations/findLocationForm.inc');

if (isset($_GET['location_query'])) {
	$template->blocks['location-panel'][] = new Block(
		'locations/findLocationResults.inc',
		array('results'=>Location::search($_GET['location_query']))
	);
}

echo $template->render();