<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET person_id_a
 * @param GET person_id_b
 */
if (!userIsAllowed('People')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

try {
	$personA = new Person($_GET['person_id_a']);
	$personB = new Person($_GET['person_id_b']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}
// When the user chooses a target, merge the other case into the target
if (isset($_POST['targetPerson'])) {
	try {
		if ($_POST['targetPerson']=='a') {
			$personA->mergeFrom($personB);
			$targetPerson = $personA;
		}
		else {
			$personB->mergeFrom($personA);
			$targetPerson = $personB;
		}

		header('Location: '.$targetPerson->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}


$template = new Template('merging');
$template->blocks[] = new Block(
	'people/mergeForm.inc',
	array('personA'=>$personA,'personB'=>$personB)
);
$template->blocks['merge-panel-one'][] = new Block(
	'people/personInfo.inc',
	array('person'=>$personA,'disableButtons'=>true)
);

$reportedCases = $personA->getReportedCases();
if (count($reportedCases)) {
	$template->blocks['merge-panel-one'][] = new Block(
		'cases/searchResults.inc',
		array(
			'caseList'=>$personA->getReportedCases(),
			'title'=>'Cases With Issues Reported By '.$personA->getFullname(),
			'disableButtons'=>true,
			'disableComments'=>true
		)
	);
}
 

$template->blocks['merge-panel-two'][] = new Block(
	'people/personInfo.inc',
	array('person'=>$personB,'disableButtons'=>true)
);
$reportedCases = $personB->getReportedCases();
if (count($reportedCases)) {
	$template->blocks['merge-panel-two'][] = new Block(
		'cases/searchResults.inc',
		array(
			'caseList'=>$personB->getReportedCases(),
			'title'=>'Cases With Issues Reported By '.$personB->getFullname(),
			'disableButtons'=>true,
			'disableComments'=>true
		)
	);
}
 
echo $template->render();