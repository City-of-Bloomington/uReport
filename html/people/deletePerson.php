<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET person_id
 */
if (userIsAllowed('People')) {
	try {
		$person = new Person($_GET['person_id']);
		$person->delete();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}
else {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
}

header('Location: '.BASE_URL.'/people');