<?php
/**
 * A Client-embeddable version of the Open311 end-user interface
 *
 * This is largely a copy of the interface provided by index.php
 * However, this version requires an api_key
 * AND outputs minimal HTML that can be embedded on client sites.
 * Register Client api_keys at BASE_URL/clients
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST api_key
 */
$user = isset($_SESSION['USER']) ? $_SESSION['USER'] : 'anonymous';
// Grab the format from the file extension used in the url
$request = explode('?',$_SERVER['REQUEST_URI']);
$format = preg_match("/\.([^.?]+)/",$request[0],$matches)
	? strtolower($matches[1])
	: 'html';
if ($format == 'php') {
	$format = 'html';
}

$template = isset($_GET['partial'])
	? new Template('partial',$format)
	: new Template('embedding',$format);

// Make sure they provide a valid api_key
if (!empty($_REQUEST['api_key'])) {
	try {
		$client = new Client($_REQUEST['api_key']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}
else {
	$_SESSION['errorMessages'][] = new Exception('clients/unknownClient');
}
if (isset($client)) {
	// Handle POSTing a report
	if (!empty($_POST['service_code'])) {
		try {
			$category = new Category($_POST['service_code']);
			if ($category->allowsPosting($user)) {
				try {
					$ticket = Open311Client::createTicket($_POST,$client);
					$ticket->save();

					// Media can only be attached after the ticket is saved
					// It uses the ticket_id in the directory structure
					if (isset($_FILES['media'])) {
						try {
							$ticket->attachMedia($_FILES['media'],0);
							$ticket->save();
						}
						catch (Exception $e) {
							// Just ignore any media errors for now
						}
					}
					$template->blocks[] = new Block('open311/thankYou.inc',array('ticket'=>$ticket));
					$template->blocks[] = new Block('open311/requestInfo.inc',array('ticket'=>$ticket));
				}
				catch (Exception $e) {
					header('HTTP/1.0 400 Bad Request',true,400);
					$_SESSION['errorMessages'][] = $e;
				}
			}
			else {
				// Not allowed to create tickets for this category
				header('HTTP/1.0 403 Forbidden',true,403);
				$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
			}
		}
		catch (Exception $e) {
			// Unknown Service Code
			header('HTTP/1.0 400 Bad Request',true,400);
			$_SESSION['errorMessages'][] = $e;
		}
	}
	// They haven't POSTed yet.  Just display the form
	else {
		$template->blocks[] = isset($_GET['partial'])
			? new Block("open311/$_GET[partial]")
			: new Block('open311/client.inc');
	}
}

echo $template->render();
