<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$user = isset($_SESSION['USER']) ? $_SESSION['USER'] : 'anonymous';

// Grab the format from the file extension used in the url
$format = preg_match("/\.([^.?]+)/",$_SERVER['REQUEST_URI'],$matches)
	? strtolower($matches[0])
	: 'html';
$template = new Template('open311',$format);



// See if they're asking for a particular request (ticket)
preg_match('|/open311/v2/requests/?([0-9a-f]{24})?.*|',$_SERVER['REQUEST_URI'],$matches);
if (isset($matches[1]) && $matches[1]) {
	try {
		$ticket = new Ticket($matches[1]);

		if (isset($_POST['description'])) {
			// Edit an existing ticket
			if (userIsAllowed('Tickets')) {

			}
			else {
				// Not allowed to edit tickets
				header('HTTP/1.0 403 Forbidden',true,403);
				$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
			}
		}
		else {
			// Display an existing ticket
			if ($ticket->allowsDisplay($user)) {
				$template->blocks[] = new Block('open311/requestInfo.inc',array('ticket'=>$ticket));
			}
			else {
				// Not allowed to see this ticket
				header('HTTP/1.0 403 Forbidden',true,403);
				$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
			}
		}
	}
	catch (Exception $e) {
		// Unknown ticket
		header('HTTP/1.0 404 Not Found',true,404);
		$_SESSION['errorMessages'][] = $e;
	}
}
else {
	if (!empty($_REQUEST['service_code'])) {
		try {
			$category = new Category($_REQUEST['service_code']);
		}
		catch (Exception $e) {
			$_SESSION['errorMessages'][] = $e;
		}
	}
	// Create a new Ticket
	if (isset($_POST['service_code'])) {
		if ($category->allowsPosting($user)) {
			try {
				$ticket = Open311Client::createTicket($_POST);
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

				$template->blocks[] = new Block('open311/requestInfo.inc',array('ticket'=>$ticket));
			}
			catch (Exception $e) {
				$_SESSION['errorMessages'][] = $e;
				switch ($e->getMessage()) {
					case 'clients/unknownClient':
						header('HTTP/1.0 403 Forbidden',true,403);
						break;

					default:
						header('HTTP/1.0 400 Bad Request',true,400);
				}
			}
		}
		else {
			// Not allowed to create tickets for this category
			header('HTTP/1.0 403 Forbidden',true,403);
			$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
		}
	}
	else {
		// Do a search for tickets
		$search = array();
		if (isset($category) && $category->allowsDisplay($user)) {
			$search['category'] = $category->getId();
		}
		if (!empty($_REQUEST['start_date'])) {
			$search['start_date'] = $_REQUEST['start_date'];
		}
		if (!empty($_REQUEST['end_date'])) {
			$search['end_date'] = $_REQUEST['end_date'];
		}
		if (!empty($_REQUEST['status'])) {
			$search['status'] = $_REQUEST['status'];
		}
		$ticketList = new TicketList($search);
		$ticketList->limit(1000);
		$template->blocks[] = new Block(
			'open311/requestList.inc',
			array('ticketList'=>$ticketList)
		);
	}
}

echo $template->render();