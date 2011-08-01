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

require_once CAS.'/SimpleCAS/Autoload.php';
$options = array('hostname'=>CAS_SERVER,'uri'=>CAS_URI);
$protocol = new SimpleCAS_Protocol_Version2($options);
$client = SimpleCAS::client($protocol);
$client->forceAuthentication();

if ($client->isAuthenticated()) {
	try {
		$_SESSION['USER'] = new Person($client->getUsername());

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
		$_SESSION['errorMessages'][] = $e;
	}
}
unset($_SESSION['return_url']);
header('Location: '.BASE_URL);
