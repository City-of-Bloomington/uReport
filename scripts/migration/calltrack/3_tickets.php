<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';
include './migrationConfig.inc';


$pdo = new PDO(MIGRATION_DSN,MIGRATION_USER,MIGRATION_PASS);

$sql = "select t.id,t.date as date,t.notes as notes,
        c.firstname as firstname,c.lastname as lastname,
        c.email as email,c.phone as phone,
        cm.name as contactMethod,s.name as status,
        u.username as username,
        res.id as resolutionId
		from requests t
        left join contacts c on t.contact_id=c.id 
		left join contactMethods cm on t.contactMethod_id=cm.id
        left join users u on t.user_id = u.id 
		left join statuses s on t.status_id=s.id
        left join resolutions res on t.id=res.request_id";

$result = $pdo->query($sql);
//
// while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
//
$results = $result->fetchAll(PDO::FETCH_ASSOC);
foreach($results as $row){
  //
	// Import the Dates
	$ticket = new Ticket();
	if ($row['date']) {
		$ticket->setEnteredDate($row['date']);
	}
	// Import the Person
	if (isset($row['username']) && $row['username']) {
	  try {
		$user = new User(strtolower($row['username']));
		$ticket->setEnteredByPerson($user->getPerson());
		if(isset($row['referralId']) && $row['referralId']){
		  $ticket->setReferredPerson($user->getPerson());
		}
		else {
		  $ticket->setAssignedPerson($user->getPerson());
		}
	  }
	  catch (Exception $e) {
	  }
	}
	// status
	switch ($row['status']) {
	case 'Requested':
	case 'Referred':			  
	  $ticket->setStatus('open');
	  break;
	default:
	  $ticket->setStatus('closed');
	}
    // if the request has resolution record ==> it is closed
    if(isset($row['resolutionId']) && $row['resolutionId']){
	  $ticket->setStatus('closed');
	  $ticket->setResolution(new Resolution('Resolved'));
    }
	// No address or location info
	$ticket->save();

	// Create the issue on this ticket
	$issue = new Issue();
	$issue->setDate($ticket->getEnteredDate());
	$issue->setTicket($ticket);
	if ($ticket->getEnteredByPerson()) {
		$issue->setPerson($ticket->getEnteredByPerson());
	}
	$issue->setNotes($row['notes']);
	if($row['contactMethod']){
	  switch ($row['contactMethod']) {
	  case 'phone':
		$issue->setContactMethod('Phone Call');
		break;
	  case 'email':			  
		$issue->setContactMethod('Email');
	  break;
	  case 'letter':			  
		$issue->setContactMethod('Letter');
	  break;	  
	  case 'in-person':			  
		$issue->setContactMethod('Walk In');
	  break;	  
	  default:  // fax
		$issue->setContactMethod('Other');
	  }
	}
	if (preg_match('/COMPLAINT/',$row['notes'])) {
		$issue->setIssueType('Complaint');
	}
	if (preg_match('/VIOLATION/',$row['notes'])) {
		$issue->setIssueType('Violation');
	}
	else {
		$issue->setIssueType('Request');
	}

	$personList = new
	  PersonList(array(
					   'firstname'=>$row['firstname'],
					   'lastname'=>$row['lastname'],
					   'email'=>$row['email'],
					   'phone'=>$row['phone']
					   ));
	if (count($personList)) {
	  $issue->setConstituent($personList[0]);
	}
	$issue->save();

	/**
	 * Create the Ticket History
	 *
	 * We're going to run through the workflow of a ticket.
	 * To help us out, we'll want to keep track of the last person who worked
	 * on the ticket at each step of the workflow
	 */
	$lastPerson = null;
	if ($ticket->getEnteredByPerson()) {
		$lastPerson = $ticket->getEnteredByPerson();
	}
	$history = new TicketHistory();
	$history->setEventLabel('opened');
	$history->setEnteredDate($ticket->getEnteredDate());
	$history->setEventDate($ticket->getEnteredDate());
	$history->setTicket($ticket);
	if ($lastPerson) {
		$history->setPerson($lastPerson);
		$history->setEventDescription("Ticket opened by {$lastPerson->getFullname()}");
	}
	else {
		$history->setEventDescription('Ticket opened');
	}
	$history->setNotes('Ticket opened');
	$history->save();
	
	//
	// looking for resolutions,responses related to this request
	// these will be considered as actions
	//
	$sql = "select date,notes,person
		from referrals 
		where request_id=".$row['id'];
	//
	$result2 = $pdo->query($sql);
	while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
	  $history = new TicketHistory();
	  $history->setEventLabel('referral');
	  $history->setEnteredDate($row2['date']);
	  $history->setEventDate($row2['date']);
	  $history->setTicket($ticket);
	  $history->setEventDescription('This ticket was referred');	  
	  $history->setNotes($row2['notes']);
	  $person = new Person();
	  $person->setFirstname($row2['person']);
	  $person->save();
	  $history->setPerson($person);
	  $history->save();
	}
	//
	// start with resolutions
	//
	$sql = "select date,notes,username
		from resolutions r
        left join users u on r.user_id = u.id 
		where r.request_id=".$row['id'];
	$result2 = $pdo->query($sql);
	while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
	  $history = new TicketHistory();
	  $history->setEventLabel('resolution');
	  $history->setEnteredDate($row2['date']);
	  $history->setEventDate($row2['date']);
	  $history->setTicket($ticket);
	  $history->setEventDescription('This ticket was resolved');
	  $history->setNotes($row['notes']);
	  $user = new User(strtolower($row2['username']));
	  $history->setPerson($user->getPerson());
	  $history->save();
	}
	//
	// responses go in issueHistory
	//
	$sql = "select r.date as date,r.notes as notes,
        u.username as username, c.name as contactMethod
		from responses r
        left join referrals rf on r.referral_id = rf.id
        left join users u on r.user_id = u.id
        left join contactMethods c on r.contactMethod_id = c.id
		where rf.request_id=".$row['id'];
	$result2 = $pdo->query($sql);
	while ($result2 && $row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
	  $history = new IssueHistory();
	  $history->setEventLabel('response');
	  $history->setEnteredDate($row2['date']);
	  $history->setEventDate($row2['date']);
	  $history->setIssue($issue);
	  $history->setEventDescription('Response to this issue by '.$row2['contactMethod']);
	  $history->setNotes($row2['notes']);	  
	  $user = new User(strtolower($row2['username']));
	  $history->setPerson($user->getPerson());
	  $history->save();
	  // print_r($history);
	}
}
