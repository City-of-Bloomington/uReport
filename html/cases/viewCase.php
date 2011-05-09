<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET case_id
 */
$case = new Case($_GET['case_id']);

$template = new Template('cases');
$template->blocks['case-panel'][] = new Block('cases/caseInfo.inc',array('case'=>$case));

if (userIsAllowed('Cases') && $case->getStatus()!='closed') {
	$template->blocks['case-panel'][] = new Block(
		'cases/actionForm.inc',
		array('case'=>$case)
	);
}

$template->blocks['history-panel'][] = new Block(
	'cases/history.inc',
	array('history'=>$case->getHistory())
);

$template->blocks['issue-panel'][] = new Block(
	'cases/issueList.inc',
	array('issueList'=>$case->getIssues(),'case'=>$case)
);

if ($case->getLocation()) {
	$template->blocks['location-panel'][] = new Block(
		'locations/locationInfo.inc',
		array('location'=>$case->getLocation())
	);

	$caseList = new CaseList(array('location'=>$case->getLocation()));
	if (count($caseList) > 1) {
		$template->blocks['location-panel'][] = new Block(
			'cases/caseList.inc',
			array(
				'caseList'=>$caseList,
				'title'=>'Other cases for this location',
				'filterCase'=>$case
			)
		);
	}
}
echo $template->render();