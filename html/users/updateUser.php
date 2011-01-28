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

// Load the user for editing
try {
	if (isset($_REQUEST['user_id']) && $_REQUEST['user_id']) {
		$user = new User($_REQUEST['user_id']);
	}
	else {
		$user = new User();
		if (isset($_REQUEST['person_id']) && $_REQUEST['person_id']) {
			$person = new Person($_REQUEST['person_id']);
			$user->setPerson($person);
		}
	}
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/users');
	exit();
}

// Handle POST data
if (isset($_POST['username'])) {
	$fields = array('username','password','authenticationMethod','roles','department_id');
	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			$set = 'set'.ucfirst($field);
			$user->$set($_POST[$field]);
		}
	}
	// Load any missing information from LDAP
	// Delete this statement if you're not using LDAP
	if ($user->getAuthenticationMethod() == 'LDAP') {
		try {
			$ldap = new LDAPEntry($user->getUsername());
			$person = $user->getPerson_id() ? $user->getPerson() : new Person();

			if (!$person->getFirstname()) {
				$person->setFirstname($ldap->getFirstname());
			}
			if (!$person->getLastname()) {
				$person->setLastname($ldap->getLastname());
			}
			if (!$person->getEmail()) {
				$person->setEmail($ldap->getEmail());
			}

			$person->save();
			if (!$user->getPerson_id()) {
				$user->setPerson($person);
			}
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
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

// Display the form
$template = new Template();
$template->blocks[] = new Block('users/updateUserForm.inc',array('user'=>$user));
if ($user->getPerson_id()) {
	$template->blocks[] = new BlocK('people/personInfo.inc',array('person'=>$user->getPerson()));
}
echo $template->render();
