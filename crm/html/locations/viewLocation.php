<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param $_GET location
 */
// Make sure we have the location in the system

$location = trim($_GET['location']);
if (!$location) {
	header('Location: '.BASE_URL.'/locations');
	exit();
}
$ticketList = new TicketList(array('location'=>$location));

$format = isset($_GET['format'])? $_GET['format']:'html';
$filename = isset($_GET['partial'])? 'partial':'locations';
$template = new Template($filename, $format);

$template->blocks['location-panel'][] = new Block(
	'locations/locationInfo.inc',
	array('location'=>$location,'disableButtons'=>isset($_GET['disableButtons']))
);
if (userIsAllowed('Tickets')) {
	$template->blocks['location-panel'][] = new Block(
		'tickets/addNewForm.inc',
		array('return_url'=>new URL(BASE_URL.'/tickets/addTicket.php'),'title'=>'Report New Case')
	);
}
$template->blocks['location-panel'][] = new Block(
	'tickets/ticketList.inc',
	array(
		'ticketList'=>$ticketList,
		'title'=>'Cases Associated with this Location',
		'disableLinks'=>isset($_GET['disableLinks']),
		'disableButtons'=>isset($_GET['disableButtons'])
	)
);

echo $template->render();
