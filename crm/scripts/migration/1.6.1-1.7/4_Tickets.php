<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once './config.inc';

function createHistory($o, $h)
{
	if ($o instanceof Ticket) {
		$history = new TicketHistory();
		$history->setTicket($o);
	}
	else {
		$history = new IssueHistory();
		$history->setIssue($o);
	}
	if (!empty($h['enteredDate'])) {
		$d = DateTime::createFromFormat('U', $h['enteredDate']->sec);
		$history->setEnteredDate($d->format('Y-m-d H:i:s'));
	}
	if (!empty($h['actionDate'])) {
		$d = DateTime::createFromFormat('U', $h['actionDate']->sec);
		$history->setActionDate($d->format('Y-m-d H:i:s'));
	}
	if (!empty($h['enteredByPerson'])) {
		$id = getPersonIdFromCrosswalk($h['enteredByPerson']['_id']);
		$history->setEnteredByPerson_id($id);
	}
	if (!empty($h['actionPerson'])) {
		$id = getPersonIdFromCrosswalk($h['actionPerson']['_id']);
		$history->setActionPerson_id($id);
	}
	if (!empty($h['action'])) {
		$history->setAction($h['action']);
	}
	if (!empty($h['notes'])) {
		$history->setNotes($h['notes']);
	}
	$history->save();
}

// Tickets
$result = $mongo->tickets->find();
foreach ($result as $r) {
	// Start a ticket record, using mongo's ticket number as the ID
	$d = DateTime::createFromFormat('U', $r['enteredDate']->sec);
	$data = array(
		'id'=>$r['number'],
		'enteredDate'=>$d->format('Y-m-d H:i:s')
	);
	if (!empty($r['category'])) {
		$c = new Category($r['category']['name']);
		$data['category_id'] = $c->getId();
	}
	$peopleFields = array('enteredByPerson', 'assignedPerson', 'referredPerson');
	foreach ($peopleFields as $f) {
		if (!empty($r[$f])) {
			$id = getPersonIdFromCrosswalk($r[$f]['_id']);
			$data[$f.'_id'] = $id;
		}
	}
	$fields = array('address_id', 'location', 'city', 'state', 'zip', 'status');
	foreach ($fields as $f) {
		if (!empty($r[$f])) {
			$data[$f] = $r[$f];
		}
	}
	if (!empty($r['coordinates'])) {
		$data['latitude']  = $r['coordinates']['latitude'];
		$data['longitude'] = $r['coordinates']['longitude'];
	}
	if (!empty($r['resolution'])) {
		try {
			$resolution = new Resolution($r['resolution']);
			$data['resolution_id'] = $resolution->getId();
		}
		catch (Exception $e) { } // Just ignore bad Resolutions
	}

	try {
		$zend_db->insert('tickets', $data);
	}
	catch (Exception $e) {
		echo "Ticket save failed {$e->getMessage()}\n";
		print_r($data);
		print_r($r);
		exit();
	}
	$ticket = new Ticket($data['id']);
	echo "Ticket: {$ticket->getId()} ";

	if (isset($r['history'])) {
		foreach ($r['history'] as $h) { createHistory($ticket, $h); }
	}

	echo "Issues";
	$count = 0;
	foreach ($r['issues'] as $i) {
		$issue = new Issue();
		$issue->setTicket($ticket);
		if (!empty($i['contactMethod'])) {
			try {
				$issue->setContactMethod(new ContactMethod($i['contactMethod']));
			}
			catch (Exception $e) { } // Just ignore bad contactMethods
		}
		if (!empty($i['responseMethod'])) {
			try {
				$issue->setResponseMethod(new ContactMethod($i['responseMethod']));
			}
			catch (Exception $e) { } // Just ignore bad contactMethods
		}
		if (!empty($i['type'])) {
			$issue->setIssueType(new IssueType($i['type']));
		}
		if (!empty($i['description'])) {
			$issue->setDescription($i['description']);
		}
		if (!empty($i['customFields'])) {
			$issue->setCustomFields($i['customFields']);
		}
		if (!empty($i['date'])) {
			$d = DateTime::createFromFormat('U', $i['date']->sec);
			$issue->setDate($d->format('Y-m-d H:i:s'));
		}
		if (!empty($i['enteredByPerson'])) {
			$id = getPersonIdFromCrosswalk($i['enteredByPerson']['_id']);
			$issue->setEnteredByPerson_id($id);
		}
		if (!empty($i['reportedByPerson'])) {
			$id = getPersonIdFromCrosswalk($i['reportedByPerson']['_id']);
			$issue->setReportedByPerson_id($id);
		}
		$issue->save();

		if (!empty($i['labels'])) {
			$labels = array();
			foreach ($i['labels'] as $l) {
				$labels[$l] = 1;
			}
			$issue->saveLabels($labels);
		}

		if (isset($i['history'])) {
			foreach ($i['history'] as $h) { createHistory($issue, $h); }
		}
		if (isset($i['responses'])) {
			foreach ($i['responses'] as $res) {
				$response = new Response();
				$response->setIssue($issue);
				if (!empty($res['date'])) {
					$d = DateTime::createFromFormat('U', $res['date']->sec);
					$response->setDate($d->format('Y-m-d H:i:s'));
				}
				if (!empty($res['contactMethod'])) {
					$response->setContactMethod(new ContactMethod($res['contactMethod']));
				}
				if (!empty($res['notes'])) {
					$response->setNotes($res['notes']);
				}
				if (!empty($res['person'])) {
					$id = getPersonIdFromCrosswalk($res['person']['_id']);
					$response->setPerson_id($id);
				}
				$response->save();
			}
		}
		if (isset($i['media'])) {
			// To Do: Handle Media files
		}
		echo "[$count] ";
	}
	echo "Done\n";
}
