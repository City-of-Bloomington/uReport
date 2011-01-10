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

if (isset($_REQUEST['user_id']) && $_REQUEST['user_id']) {
	try {
		$user = new User($_REQUEST['user_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}
else {
	$user = new User();
}

if (isset($_POST['username'])) {
	$fields = array(
		'username','password','authenticationMethod','firstname','lastname','email','roles'
	);

	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			$set = 'set'.ucfirst($field);
			$user->$set($_POST[$field]);
		}
	}

	// Load their information from LDAP
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

$template = new Template('two-column');
$template->blocks[] = new Block('users/updateUserForm.inc',array('user'=>$user));
echo $template->render();
