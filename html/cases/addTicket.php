<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Cases')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$case = new Case();
$issue = new Issue();

// If the user has chosen a location, they'll pass it in here
if (isset($_GET['location']) && $_GET['location']) {
	$case->setLocation($_GET['location']);
	$case->setAddressServiceData(AddressService::getLocationData($case->getLocation()));
}
if (isset($_REQUEST['person_id'])) {
	$issue->setReportedByPerson($_REQUEST['person_id']);
}

if(isset($_POST['case'])){
	// Create the case
	$fields = array(
		'location','latitude','longitude','address_id','city','state','zip'
	);
	foreach ($fields as $field) {
		if (isset($_POST['case'][$field])) {
			$set = 'set'.ucfirst($field);
			$case->$set($_POST['case'][$field]);
		}
	}
	$case->setAddressServiceData(AddressService::getLocationData($case->getLocation()));

	$case->setAssignedPerson($_POST['assignedPerson']);
	$case->setEnteredByPerson($_SESSION['USER']);

	// Create the issue
	$fields = array(
		'type','reportedByPerson',
		'contactMethod','responseMethod',
		'category','notes'
	);
	foreach ($fields as $field) {
		$set = 'set'.ucfirst($field);
		$issue->$set($_POST['issue'][$field]);
	}
	$issue->setEnteredByPerson($_SESSION['USER']);

	// Create the History entries
	$open = new History();
	$open->setAction('open');
	$open->setEnteredByPerson($_SESSION['USER']);
	$open->setActionPerson($_SESSION['USER']);

	$assignment = new History();
	$assignment->setAction('assignment');
	$assignment->setEnteredByPerson($_SESSION['USER']);
	$assignment->setActionPerson($_POST['assignedPerson']);

	// Validate Everything and save
	try {
		$case->updateIssues($issue);
		$case->updateHistory($open);
		$case->updateHistory($assignment);
		$case->save();

		header('Location: '.$case->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}


$template = new Template('caseCreation');

$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

if ($case->getLocation()) {
	$template->blocks['location-panel'][] = new Block(
		'locations/locationInfo.inc',
		array('location'=>$case->getLocation())
	);

	$template->blocks['location-panel'][] = new Block(
		'cases/caseList.inc',
		array(
			'caseList'=>new CaseList(array('location'=>$case->getLocation())),
			'title'=>'Cases Associated with this Location'
		)
	);
}
else {
	$template->blocks['location-panel'][] = new Block(
		'locations/findLocationForm.inc',
		array('return_url'=>$return_url,'includeExternalResults'=>true)
	);
}

$personPanel = $issue->getReportedByPerson()
	? new Block('people/personInfo.inc',array('person'=>new Person($issue->getReportedByPerson())))
	: new Block('people/searchForm.inc',array('return_url'=>$return_url));
$template->blocks['person-panel'][] = $personPanel;

$addCaseForm = new Block(
	'cases/addCaseForm.inc',
	array('case'=>$case,'issue'=>$issue)
);
$template->blocks['case-panel'][] = $addCaseForm;

echo $template->render();
