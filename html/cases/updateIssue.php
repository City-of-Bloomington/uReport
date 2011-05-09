<?php
/**
 * The controller for handling issue editing.
 *
 * Choosing a person involves going through a whole person finding process
 * at a different url.  Once the user has chosen a new person, they will
 * return here, passing in the person_id they have chosen
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST issue_id
 * @param REQUEST person_id The new person to apply to the issue
 */
//-------------------------------------------------------------------
// Make sure they're supposed to be here
//-------------------------------------------------------------------
if (!userIsAllowed('Issues')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

//-------------------------------------------------------------------
// Load all the data that's passed in
//-------------------------------------------------------------------
try {
	$case = new Case($_REQUEST['case_id']);
	$issues = $case->getIssues();
	if (isset($_REQUEST['index']) && array_key_exists($_REQUEST['index'],$issues)) {
		$issue = $issues[$_REQUEST['index']];
		$index = (int)$_REQUEST['index'];
	}
	else {
		$issue = new Issue();
		$index = null;
	}
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/cases');
	exit();
}

if (isset($_REQUEST['person_id'])) {
	$issue->setReportedByPerson($_REQUEST['person_id']);
}

//-------------------------------------------------------------------
// Handle any stuff the user posts
//-------------------------------------------------------------------
if (isset($_POST['issue'])) {
	if (!$issue->getEnteredByPerson) {
		$issue->setEnteredByPerson($_SESSION['USER']);
	}
	$fields = array(
		'type','reportedByPerson','contactMethod','responseMethod','category','notes'
	);
	foreach ($fields as $field) {
		$set = 'set'.ucfirst($field);
		$issue->$set($_POST['issue'][$field]);
	}
	$case->updateIssues($issue,$index);

	try {
		$case->save();
		header('Location: '.$case->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

//-------------------------------------------------------------------
// Display the view
//-------------------------------------------------------------------
$template = new Template('cases');
$template->blocks['case-panel'][] = new Block(
	'cases/caseInfo.inc',
	array('case'=>$case,'disableButtons'=>true)
);
$template->blocks['history-panel'][] = new Block(
	'cases/history.inc',
	array('history'=>$case->getHistory())
);
$template->blocks['issue-panel'][] = new Block(
	'cases/updateIssueForm.inc',
	array('case'=>$case,'index'=>$index,'issue'=>$issue)
);
$template->blocks['location-panel'][] = new Block(
	'locations/locationInfo.inc',
	array('location'=>$case->getLocation())
);
if ($case->getLocation()) {
	$template->blocks['location-panel'][] = new Block(
		'cases/caseList.inc',
		array(
			'caseList'=>new CaseList(array('location'=>$case->getLocation())),
			'title'=>'Other cases for this location',
			'disableButtons'=>true,
			'filterCase'=>$case
		)
	);
}
echo $template->render();
