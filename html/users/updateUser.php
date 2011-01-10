<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST user_id
 */
if (!userIsAllowed('Users')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$user = new User($_REQUEST['user_id']);

if (isset($_POST['user'])) {
	foreach ($_POST['user'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$user->$set($value);
	}

	try {
		$user->save();
		header('Location: '.BASE_URL.'/users');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->blocks[] = new Block('users/updateUserForm.inc',array('user'=>$user));
$template->blocks[] = new BlocK('people/personInfo.inc',array('person'=>$user->getPerson()));
echo $template->render();
