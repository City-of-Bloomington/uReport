<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST case_id
 * @param REQUEST person_id
 */
// Make sure they're supposed to be here
if (!userIsAllowed('Cases')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the Case and Person
try {
	$case = new Case($_REQUEST['case_id']);
	$person = new Person($_REQUEST['person_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

// Handle any stuff the user posts
if (isset($_POST['referredPerson'])) {
	try {
		$case->setReferredPerson($_POST['referredPerson']);

		// add a record to case history
		$history = new History();
		$history->setAction('referral');
		$history->setEnteredByPerson($_SESSION['USER']);
		$history->setActionPerson($case->getReferredPerson());
		$history->setNotes($_POST['notes']);
		$case->updateHistory($history);

		$case->save();
		header('Location: '.$case->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

// Display the view
$template = new Template('cases');
$template->blocks['case-panel'][] = new Block(
	'cases/caseInfo.inc',
	array('case'=>$case,'disableButtons'=>true)
);
$template->blocks['case-panel'][] = new Block(
	'cases/referCaseForm.inc',
	array('case'=>$case,'person'=>$person)
);
$template->blocks['history-panel'][] = new Block(
	'cases/history.inc',
	array('history'=>$case->getHistory(),'disableButtons'=>true)
);
$template->blocks['issue-panel'][] = new Block(
	'cases/issueList.inc',
	array('issueList'=>$case->getIssues(),'disableButtons'=>true)
);
if ($case->getLocation()) {
	$template->blocks['location-panel'][] = new Block(
		'locations/locationInfo.inc',
		array('location'=>$case->getLocation())
	);
	$template->blocks['location-panel'][] = new Block(
		'cases/caseList.inc',
		array(
			'caseList'=>new CaseList(array('location'=>$case->getLocation())),
			'title'=>'Other cases for this location',
			'disableButtons'=>true
		)
	);
}
echo $template->render();
