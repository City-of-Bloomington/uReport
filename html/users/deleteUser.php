<?php
/**
 * Removes all the user account information from a Person
 *
 * The person will still exist in the system, they just won't be able to log in.
 *
 * @copyright 2006-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET user_id
 */
if (!userIsAllowed('Users')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$person = new Person($_GET['user_id']);
$person->deleteUserAccount();
try {
	$person->save();
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
}

header('Location: '.BASE_URL.'/users');
