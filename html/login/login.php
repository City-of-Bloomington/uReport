<?php
/**
 *	Logs a user into the system.
 *
 *	A logged in user will have a $_SESSION['USER']
 *
 * @copyright 2006-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
try {
	$person = new Person($_POST['username']);
	if ($person->authenticate($_POST['password'])) {
		$_SESSION['USER'] = $person;
	}
	else {
		throw new Exception('wrongPassword');
	}
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

// The user has successfully logged in.  Redirect them wherever you like
if ($_POST['return_url']) {
	header('Location: '.$_POST['return_url']);
}
else {
	header('Location: '.BASE_URL);
}
