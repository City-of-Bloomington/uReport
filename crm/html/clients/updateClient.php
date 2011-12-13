<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Clients')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the $client for editing
if (isset($_REQUEST['client_id']) && $_REQUEST['client_id']) {
	try {
		$client = new Client($_REQUEST['client_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/clients');
		exit();
	}
}
else {
	$client = new Client();
}

if (isset($_POST['name'])) {
	$client->set($_POST);
	try {
		$client->save();
		header('Location: '.BASE_URL.'/clients');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('clients/updateClientForm.inc',array('client'=>$client));
echo $template->render();