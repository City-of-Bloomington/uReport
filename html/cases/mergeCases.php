<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET case_id_a
 * @param GET case_id_b
 */
if (!userIsAllowed('Cases')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the two cases
try {
	$caseA = new Case($_REQUEST['case_id_a']);
	$caseB = new Case($_REQUEST['case_id_b']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

// When the user chooses a target, merge the other case into the target
if (isset($_POST['targetCase'])) {
	try {
		if ($_POST['targetCase']=='a') {
			$caseA->mergeFrom($caseB);
			$targetCase = $caseA;
		}
		else {
			$caseB->mergeFrom($caseA);
			$targetCase = $caseB;
		}

		header('Location: '.$targetCase->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

// Display the form
$template = new Template('merging');
$template->blocks[] = new Block(
	'cases/mergeForm.inc',
	array('caseA'=>$caseA,'caseB'=>$caseB)
);

$template->blocks['merge-panel-one'][] = new Block(
	'cases/caseInfo.inc',
	array('case'=>$caseA,'disableButtons'=>true)
);
$template->blocks['merge-panel-one'][] = new Block(
	'cases/history.inc',
	array('caseHistory'=>$caseA->getHistory(),'disableComments'=>true)
);
$template->blocks['merge-panel-one'][] = new Block(
	'cases/issueList.inc',
	array(
		'issueList'=>$caseA->getIssues(),
		'case'=>$caseA,
		'disableButtons'=>true,
		'disableComments'=>true
	)
);

$template->blocks['merge-panel-two'][] = new Block(
	'cases/caseInfo.inc',
	array('case'=>$caseB,'disableButtons'=>true)
);
$template->blocks['merge-panel-two'][] = new Block(
	'cases/history.inc',
	array('caseHistory'=>$caseB->getHistory(),'disableComments'=>true)
);
$template->blocks['merge-panel-two'][] = new Block(
	'cases/issueList.inc',
	array(
		'issueList'=>$caseB->getIssues(),
		'case'=>$caseB,
		'disableButtons'=>true,
		'disableComments'=>true
	)
);
echo $template->render();