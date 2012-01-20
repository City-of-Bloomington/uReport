<?php
/**
 *	Logs a user into the system.
 *
 *	A logged in user will have a $_SESSION['USER']
 *
 * @copyright 2006-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$return_url = isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL;

if (isset($_POST['username'])) {
	try {
		$person = new Person($_POST['username']);
		if ($person->authenticate($_POST['password'])) {
			$_SESSION['USER'] = $person;

			// The user has successfully logged in.  Redirect them wherever you like
			if ($_POST['return_url']) {
				header('Location: '.$_POST['return_url']);
			}
			else {
				header('Location: '.BASE_URL.'/tickets');
			}
		}
		else {
			throw new Exception('wrongPassword');
		}
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->blocks[] = new Block('loginForm.inc',array('return_url'=>$return_url));
echo $template->render();