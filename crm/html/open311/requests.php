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
	if (isset($_POST['service_code'])) {
		// Create a new Ticket
		try {
			if (!isset($category)) {
				throw new Exception('missingService');
			}
			if ($category->allowsPosting($user)) {
				$ticketData = array();
				$issueData = array();

				// Translate Open311 fields into CRM fields
				$open311Fields = array(
					'ticketData'=>array(
						'lat'=>'latitude',
						'long'=>'longitude',
						'address_string'=>'location'
					),
					'issueData'=>array(
						'description'=>'description',
						'attribute'=>'customFields'
					)
				);
				foreach ($_POST as $key=>$value) {
					// Attributes will come in as an array, but we still
					// want to trim all the strings
					$value = is_string($value) ? trim($value) : $value;

					if ($value) {
						if (isset($open311Fields['ticketData'][$key])) {
							$ticketData[$open311Fields['ticketData'][$key]] = $value;
						}
						elseif (isset($open311Fields['issueData'][$key])) {
							$issueData[$open311Fields['issueData'][$key]] = $value;
						}
					}
				}

				$ticket = new Ticket();
				$ticket->setCategory($category);
				$ticket->set($ticketData);

				$issue = new Issue();
				$issue->set($issueData);

				// See if we can figure out who they're claiming to be
				$personFields = array(
					'first_name'=>'firstname',
					'last_name'=>'lastname',
					'email'=>'email',
					'phone'=>'phone.number',
					'device_id'=>'phone.device_id'
				);
				$search = array();
				foreach ($personFields as $open311Field=>$crmField) {
					if (!empty($_POST[$open311Field])) {
						$search[$crmField] = $_POST[$open311Field];
					}
				}
				if (count($search)) {
					$list = new PersonList($search);
					// If find exactly one person that matches, report the issue as that person
					if (count($list) == 1) {
						foreach ($list as $person) {
							$issue->setReportedByPerson($person);
						}
					}
					// Else add a new person with the info they gave us
					else {
						$person = new Person();
						$personFields = array(
							'first_name'=>'firstname',
							'last_name'=>'lastname',
							'email'=>'email',
							'phone'=>'phoneNumber',
							'device_id'=>'phoneDeviceId'
						);
						foreach ($personFields as $key=>$field) {
							if (!empty($_POST[$key])) {
								$set = 'set'.ucfirst($field);
								$person->$set($_POST[$key]);
							}
						}
						try {
							$person->save();
							$issue->setReportedByPerson($person);
						}
						catch (Exception $e) {
							// Not sure if we should send an error message or not.
							// For now, just ignore
						}
					}
				}
				$ticket->updateIssues($issue);

				// Try and save the ticket
				try {
					$ticket->save();

					// Media can only be attached after the ticket is saved
					// It uses the ticket_id in the directory structure
					// Then, we need to save the ticket again to store all
					// the media's metadata in the ticket
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
			header('HTTP/1.0 400 Bad Request',true,400);
			$_SESSION['errorMessages'][] = $e;
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