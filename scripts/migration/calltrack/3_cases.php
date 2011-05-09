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
  $case = new case();
  if ($row['date']) {
	$case->setEnteredDate($row['date']);
  }
  // Import the Person
  if (isset($row['username']) && $row['username']) {
	try {
	  $case->setEnteredByPerson($row['username']);
	  if(isset($row['referralId']) && $row['referralId']){
		$case->setReferredPerson($row['referredId']);
	  }
	  else {
		$case->setAssignedPerson($row['username']);
	  }
	}
	catch (Exception $e) {
	}
  }
  // status
  switch ($row['status']) {
  case 'Requested':
  case 'Referred':			  
	$case->setStatus('open');
	break;
  default:
	$case->setStatus('closed');
	$case->setResolution('Resolved');
  }
  // if the request has resolution record ==> it is closed
  if(isset($row['resolutionId']) && $row['resolutionId']){
	$case->setStatus('closed');
	$case->setResolution('Resolved');
  }
  // No address or location info
 //  $case->save();

  // Create the issue on this case
  $issue = new Issue();
  $issue->setDate($case->getEnteredDate());
  if ($case->getEnteredByPerson()) {
	$issue->setEnteredByPerson($case->getEnteredByPerson());
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
	$issue->setType('Complaint');
  }
  if (preg_match('/VIOLATION/',$row['notes'])) {
	$issue->setType('Violation');
  }
  else {
	$issue->setType('Request');
  }
  
  $personList = new
	PersonList(array(
					 'firstname'=>$row['firstname'],
					 'lastname'=>$row['lastname'],
					 'email'=>$row['email'],
					 'phone'=>$row['phone']
					 ));
  if (count($personList)) {
	$personList->next();
	$issue->setReportedByPerson($personList->current());
  }
  $case->updateIssues($issue);	
  /**
   * Create the case History
   *
   * We're going to run through the workflow of a case.
   * To help us out, we'll want to keep track of the last person who worked
   * on the case at each step of the workflow
   */
  $lastPerson = null;
  if ($case->getEnteredByPerson()) {
	$lastPerson = $case->getEnteredByPerson();
  }
  $history = new History();
  $history->setAction('open');
  $history->setEnteredDate($case->getEnteredDate());
  $history->setActionDate($case->getEnteredDate());
  if ($lastPerson) {
	$history->setEnteredByPerson($lastPerson);
  }
  $case->updateHistory($history);
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
	$history = new History();
	$history->setAction('referral');
	$history->setEnteredDate($row2['date']);
	$history->setActionDate($row2['date']);
	$history->setNotes($row2['notes']);
	if ($lastPerson) {
	  $history->setEnteredByPerson($lastPerson);
	}
	$personList = null;
	$list = explode(' ',$row2['person']);
	if(count($list) > 1){
	  $personList = new PersonList(array('firstname'=>$list[0],
										 'lastname'=>$list[1]));
	  // if (count($personList)) {
	  //	$history->setActionPerson($personList[0]);
	  //}
	  if (!count($personList)) {  
		$personList = new PersonList(array('firstname'=>$row2['person']));
	  }
	}
	if($personList && count($personList)){
		$personList->next();
	  $history->setActionPerson($personList->current());							   
	}
	else{
	  // if no match found, we put all in first name
	  $person = new Person();
	  $person->setFirstname($row2['person']); 
	  $person->save();
	  $history->setActionPerson($person);		
	}
	$case->updateHistory($history);
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
	$history = new History();
	$history->setAction('close');
	$history->setActionDate($row2['date']);
	$history->setEnteredDate($row2['date']);
	$history->setNotes($row2['notes']);
	$history->setEnteredByPerson($row2['username']);		
	$history->setActionPerson($row2['username']);
	$case->updateHistory($history);
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
	$history = new History();
	$history->setAction('response');
	$history->setEnteredDate($row2['date']);
	$history->setActionDate($row2['date']);
	$history->setNotes($row2['notes']);	  
	$history->setEnteredByPerson($row2['username']);
	$history->setActionPerson($row2['username']);
	/*
	if($row2['contactMethod']){
	  switch ($row2['contactMethod']) {
	  case 'phone':
		$history->setContactMethod('Phone Call');
		break;
	  case 'email':			  
		$history->setContactMethod('Email');
		break;
	  case 'letter':			  
		$history->setContactMethod('Letter');
		break;	  
	  case 'in-person':			  
		$history->setContactMethod('Walk In');
		break;	  
	  default:  // fax
		$history->setContactMethod('Other');
	  }
	}
	*/
  	$case->updateHistory($history);
	// print_r($history);
  }
  try {
  	$case->save();
  }
  catch (Exception $e) {
  	echo $e->getMessage()."\n";
	print_r($e);
  	exit();
  }
}
