<?php
/**
 * @copyright 2009-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST user_id
 */
if (!userIsAllowed('Users')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

if (isset($_REQUEST['person_id'])) {
	// Load the user for editing
	try {
		$user = new Person($_REQUEST['person_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/users');
		exit();
	}
}
else {
	$user = new Person();
}

// Handle POST data
if (isset($_POST['username'])) {
	$user->setUsername($_POST['username']);
	$user->setAuthenticationMethod($_POST['authenticationMethod']);
	$user->setDepartment($_POST['department']);

	$roles = isset($_POST['roles']) ? $_POST['roles'] : array();
	$user->setRoles($roles);

	if (isset($_POST['password']) && $_POST['password']) {
		$user->setPassword($_POST['password']);
	}

	// Load any missing information from LDAP
	// You can delete this statement if you're not using LDAP
	if (array_key_exists($user->getAuthenticationMethod(),$LDAP_CONFIG)) {
		try {
			$ldap = new LDAP(
				$LDAP_CONFIG[$user->getAuthenticationMethod()],
				$user->getUsername()
			);
			$user->populateFromLDAP($ldap);
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
			print_r($e);
			exit();
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
$template->blocks[] = new Block('users/updateUserForm.inc',array('person'=>$user));
echo $template->render();

