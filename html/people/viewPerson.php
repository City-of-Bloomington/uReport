<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET person_id
 */
if (!isset($_GET['person_id'])) {
	header('Location: '.BASE_URL.'/people');
	exit();
}
try {
	$person = new Person($_GET['person_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/people');
	exit();
}

$template = new Template('people');
$template->title = $person->getFullname();
$template->blocks['person-panel'][] = new Block('people/personInfo.inc',array('person'=>$person));

$reportedCases = $person->getReportedCases();
if (count($reportedCases)) {
	$template->blocks['case-panel'][] = new Block(
		'cases/caseList.inc',
		array(
			'caseList'=>$person->getReportedCases(),
			'title'=>'Cases With Issues Reported By '.$person->getFullname()
		)
	);
}

echo $template->render();
