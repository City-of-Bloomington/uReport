<?php
/**
 * @copyright 2009-2011 City of Bloomington, Indiana
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
	$user = new Person($_REQUEST['user_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/users');
	exit();
}

// Handle POST data
if (isset($_POST['username'])) {
	$fields = array('username','password','authenticationMethod','roles','department');
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

			if (!$user->getFirstname()) {
				$user->setFirstname($ldap->getFirstname());
			}
			if (!$user->getLastname()) {
				$user->setLastname($ldap->getLastname());
			}
			if (!$user->getEmail()) {
				$user->setEmail($ldap->getEmail());
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
$template = new Template('two-column');
$template->blocks[] = new Block('people/personInfo.inc',array('person'=>$user));
$template->blocks[] = new Block('users/updateUserForm.inc',array('user'=>$user));
echo $template->render();

