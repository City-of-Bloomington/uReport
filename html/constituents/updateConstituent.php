<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST constituent_id
 */
if (!userIsAllowed('Constituents')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the $constituent for editing
if (isset($_REQUEST['constituent_id']) && $_REQUEST['constituent_id']) {
	try {
		$constituent = new Constituent($_REQUEST['constituent_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/constituents');
		exit();
	}
}
else {
	$constituent = new Constituent();
}


if (isset($_POST['constituent_id'])) {
	$fields = array(
		'firstname','lastname','middlename','salutation',
		'address','city','state','zip','email'
	);

	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			$set = 'set'.ucfirst($field);
			$constituent->$set($_POST[$field]);
		}
	}

	try {
		$constituent->save();

		$return_url = (isset($_POST['return_url']) && $_POST['return_url'])
			? $_POST['return_url']
			: BASE_URL.'/constituents';
		header("Location: $return_url");
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->blocks[] = new Block('constituents/updateConstituentForm.inc',array('constituent'=>$constituent));
echo $template->render();