<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$return_url = isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL;

// If they don't have CAS configured, send them onto the application's
// internal authentication system
if (!defined('CAS')) {
	header('Location: '.BASE_URL.'/login.php?return_url='.$return_url);
	exit();
}

$_SESSION['return_url'] = $return_url;

require_once CAS.'/CAS.php';
phpCAS::client(CAS_VERSION_2_0, CAS_SERVER, 443, CAS_URI, false);
phpCAS::setNoCasServerValidation();
phpCAS::forceAuthentication();
// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().

// They may be authenticated according to CAS,
// but that doesn't mean they have person record
// and even if they have a person record, they may not
// have a user account for that person record.
try {
	$_SESSION['USER'] = new Person(phpCAS::getUser());


	if (isset($_SESSION['return_url'])) {
		$return_url = $_SESSION['return_url'];
		unset($_SESSION['return_url']);

		header('Location: '.$return_url);
		exit();
	}
	else {
		header('Location: '.BASE_URL);
		exit();
	}
}
catch (Exception $e) {
	// They authentcated against CAS,
	// but do not have a Person record
	$_SESSION['errorMessages'][] = $e;
}
