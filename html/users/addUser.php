<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET person_id
 */
if (!userIsAllowed('Users')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

if (isset($_REQUEST['person_id'])) {
	try {
		$person = new Person($_REQUEST['person_id']);
	}
	catch (Exception $e) {
	}
}

if (isset($_POST['user'])) {

	$user = new User();
	foreach ($_POST['user'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$user->$set($value);
	}

	if (isset($person)) {
		$user->setPerson_id($person->getId());
	}
	else {
		// Load their information from LDAP
		// Delete this statement if you're not using LDAP
		if ($user->getAuthenticationMethod() == 'LDAP') {
			try {
				$ldap = new LDAPEntry($user->getUsername());
				try {
					$person = new Person($ldap->getEmail());
				}
				catch (Exception $e) {
					$person = new Person();
					$person->setFirstname($ldap->getFirstname());
					$person->setLastname($ldap->getLastname());
					$person->setEmail($ldap->getEmail());
					$person->save();
				}
				$user->setPerson($person);
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
			}
		}
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
$template->title = 'Create a user account';
$template->blocks[] = new Block('users/addUserForm.inc');
if (isset($person)) {
	$template->blocks[] = new Block('people/personInfo.inc',array('person'=>$person));
}
echo $template->render();
