<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!isset($_SESSION['USER'])) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL.'/admin');
	exit();
}

if (isset($_POST['firstname'])) {
	$fields = array(
		'firstname','middlename','lastname','email','phone','organization',
		'address','city','state','zip'
	);
	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			$set = 'set'.ucfirst($field);
			$_SESSION['USER']->$set($_POST[$field]);
		}
	}

	try {
		$_SESSION['USER']->save();
		header('Location: '.BASE_URL.'/admin');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block(
	'people/updatePersonForm.inc',
	array('person'=>$_SESSION['USER'],'title'=>'Update my info')
);
echo $template->render();
