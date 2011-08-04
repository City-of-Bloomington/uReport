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
	if (!$_SESSION['USER']->getUsername()) {
		$_SESSION['USER']->setUsername(phpCAS::getUser());
		$_SESSION['USER']->setAuthenticationMethod('PublicLDAP');
		try {
			$_SESSION['USER']->save();
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}
	}
}
catch (Exception $e) {
	// They authenticated against CAS, but do not have a Person record
	// Find their LDAP accounts, and create a Person record for them
	foreach ($LDAP_CONFIG as $key=>$config) {
		try {
			$ldap = new LDAP($config,phpCAS::getUser());
			$_SESSION['USER'] = new Person();
			$_SESSION['USER']->setUsername(phpCAS::getUser());
			$_SESSION['USER']->setAuthenticationMethod($key);
			$_SESSION['USER']->populateFromLDAP($ldap);
			$_SESSION['USER']->save();
			break;
		}
		catch (Exception $e) {
		}
	}
	if (!isset($_SESSION['USER'])) {
		// We could not find their LDAP account and could not create a person record
		$_SESSION['errorMessages'][] = $e;
	}
}

// Send them back to where they came from
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
