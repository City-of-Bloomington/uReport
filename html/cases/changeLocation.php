<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST case_id
 */
if (!userIsAllowed('Cases')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

try {
	$case = new Case($_REQUEST['case_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

// Once the user has chosen a location, they'll pass it in here
if (isset($_REQUEST['location']) && $_REQUEST['location']) {
	$case->clearAddressServiceCache();
	$case->setLocation($_REQUEST['location']);
	$case->setAddressServiceCache(AddressService::getLocationData($case->getLocation()));
	try {
		$case->save();
		header('Location: '.$case->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

$template = new Template('cases');
$template->blocks['case-panel'][] = new Block(
	'locations/findLocationForm.inc',
	array('return_url'=>$return_url,'includeExternalResults'=>true)
);

$template->blocks['history-panel'][] = new Block(
	'cases/history.inc',
	array('caseHistory'=>$case->getHistory())
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