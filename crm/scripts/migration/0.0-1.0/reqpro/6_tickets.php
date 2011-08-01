<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';
include './migrationConfig.inc';
include './categoryTranslation.inc';

$UNFOUND_PEOPLE = fopen('./unfoundPeople.log','w');

$unknownPerson = new Person();
$unknownPerson->setFirstname('unknown');
$unknownPerson->setLastname('person');
$unknownPerson->setUsername('unknown');
$unknownPerson->save();

$townships = array(
	1=>'Bean Blossom',
	2=>'Benton',
	3=>'Bloomington',
	4=>'Clear Creek',
	5=>'Indian Creek',
	6=>'Perry',
	7=>'Van Buren',
	#8=>'Unknown', // Don't import unknown townships
	9=>'Richland',
	10=>'Polk',
	11=>'Washington',
	12=>'Salt Creek'
);

$pdo = new PDO(MIGRATION_DSN,MIGRATION_USER,MIGRATION_PASS);

$sql = "select c.*, t.comp_desc, i.username, i.needed, i.existing
		from ce_eng_comp c
		left join c_types t on c.c_type=t.c_type1
		left join inspectors i on c.inspector=i.inspector";
$result = $pdo->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

	$location = "$row[street_num] $row[street_dir] $row[street_name] $row[street_type] $row[sud_type] $row[sud_num]";
	$location = preg_replace('/\s+/',' ',$location);
	echo "$location ==> ";

	// Import the Dates
	$ticket = new Ticket();
	if ($row['received']) {
		$ticket->setEnteredDate($row['received']);
	}
	else {
		continue;
	}

	// Status
	switch ($row['status']) {
		case 'NOT VALID':
		case 'NOT PROCESSED':
			$ticket->setStatus('closed');
			$ticket->setResolution('Bogus');
			break;
		case 'COMPLETED':
			$ticket->setStatus('closed');
			$ticket->setResolution('Resolved');
			break;
		default:
			$ticket->setStatus('open');
	}
	// ReqPro was not very good at keeping it's status and completed_date in sync
	if ($row['completed_date'] && $ticket->getStatus()=='open') {
		$ticket->setStatus('closed');
	}

	// Import the Person
	if (isset($row['received_by']) && $row['received_by']) {
		try {
			$ticket->setEnteredByPerson($row['received_by']);
		}
		catch (Exception $e) {
		}
	}
	if (!$ticket->getEnteredByPerson()) {
		$ticket->setEnteredByPerson('unknown');
	}
	if (isset($row['assigned_to']) && $row['assigned_to']) {
		try {
			list($username,$fullname) = explode(':',$row['assigned_to']);
			$person = new Person($username);
			$ticket->setAssignedPerson($person);
		}
		catch (Exception $e) {
		}
	}
	// If they didn't assign it to anyone in particular,
	// try and assign it to the default person for the department
	elseif (isset($row['dept'])) {
		try {
			$department = new Department($row['dept']);
			$ticket->setAssignedPerson($department->getDefaultPerson());
		}
		catch (Exception $e) {
		}
	}

	// Check the location against Master Address
	// Master Address data should overwrite information from ReqPro
	$row['street_num'] = preg_replace('/[^a-zA-Z0-9\-\&\s\'\/]/','',$row['street_num']);
	if ($row['street_num']) {
		$query = preg_match('/[a-zA-Z]/',$row['street_num'])
			? $row['street_num']
			: "$row[street_num] $row[street_dir] $row[street_name] $row[street_type] $row[sud_type] $row[sud_num]";
		$data = AddressService::getLocationData($query);


		echo $query.' ==> ';
		if (count($data)) {
			$ticket->setAddressServiceData($data);
		}
		else {
			if (is_numeric($row['street_num'])) {
				$location = "$row[street_num] $row[street_dir] $row[street_name] $row[street_type] $row[sud_type] $row[sud_num]";
				$location = preg_replace('/\s+/',' ',$location);
				$ticket->setLocation($location);
			}
			else {
				$ticket->setLocation($row['street_num']);
			}
		}
	}
	else {
		$location = "$row[street_dir] $row[street_name] $row[street_type] $row[sud_type] $row[sud_num]";
		$location = preg_replace('/\s+/',' ',$location);
		$ticket->setLocation($location);
	}
	echo $ticket->getLocation()."\n";

	// Create the issue on this ticket
	$issue = new Issue();
	$issue->setDate($ticket->getEnteredDate());
	if ($ticket->getEnteredByPerson()) {
		$issue->setEnteredByPerson($ticket->getEnteredByPerson());
	}
	if ($row['comments']) {
		$issue->setNotes($row['comments']);
	}
	if ($row['complaint_source']) {
		$issue->setContactMethod($row['complaint_source']);
	}
	if (preg_match('/COMPLAINT/',$row['comp_desc'])) {
		$issue->setType('Complaint');
	}
	if (preg_match('/VIOLATION/',$row['comp_desc'])) {
		$issue->setType('Violation');
	}
	else {
		$issue->setType('Request');
	}
	if ($row['comp_desc']) {
		$row['comp_desc'] = trim($row['comp_desc']);
		$category = isset($CATEGORIES[$row['comp_desc']]) ? $CATEGORIES[$row['comp_desc']] : $row['comp_desc'];
		$issue->setCategory($category);
	}

	if ($row['first_name'] || $row['middle_initial'] || $row['last_name'] || $row['e_mail_address']) {
		$personList = new PersonList(array(
			'firstname'=>ucwords(strtolower($row['first_name'])),
			'middlename'=>ucwords(strtolower($row['middle_initial'])),
			'lastname'=>ucwords(strtolower($row['last_name'])),
			'email'=>strtolower($row['e_mail_address'])
		));
		if (count($personList)) {
			$personList->next();
			$issue->setReportedByPerson($personList->current());
		}
		else {
			fwrite($UNFOUND_PEOPLE,"$row[first_name]|$row[middle_initial]|$row[last_name]|$row[e_mail_address]\n");
		}
	}
	$ticket->updateIssues($issue);

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

	$history = new History();
	$history->setAction('open');
	$history->setEnteredDate($ticket->getEnteredDate());
	$history->setActionDate($ticket->getEnteredDate());
	if ($lastPerson) {
		$history->setEnteredByPerson($lastPerson);
		$history->setActionPerson($lastPerson);
	}
	$ticket->updateHistory($history);

	if ($row['assigned_to']) {
		$history = new History();
		$history->setAction('assignment');
		$history->setEnteredDate($row['assigned_date']);
		$history->setActionDate($row['assigned_date']);
		$history->setNotes("$row[action_taken]\n$row[next_action]");
		// The ticket must have been assigned by the last person who worked on the ticket
		if ($lastPerson) {
			$history->setEnteredByPerson($lastPerson);
		}

		// If we know who assigned it use them.
		// Otherwise, assign it to the person who created the ticket
		if ($ticket->getAssignedPerson()) {
			$history->setActionPerson($ticket->getAssignedPerson());
		}
		elseif ($ticket->getEnteredByPerson()) {
			$history->setActionPerson($ticket->getEnteredByPerson());
		}

		// Assignments really do need a person
		// Saying something is assigned without knowing who is useless.
		if ($history->getActionPerson()) {
			$lastPerson = $history->getActionPerson();
			try {
				$ticket->updateHistory($history);
			}
			catch  (Exception $e) {
				// Any problems with the action, and we won't save it
				// These problems should all be assignments to people we don't
				// have in the system
				echo "Couldn't save assignment\n";
				echo $e->getMessage()."\n";
				print_r($history);
				exit();
			}
		}
	}

	if ($row['insp_date']) {
		$history = new History();
		$history->setAction('inspection');
		$history->setEnteredDate($row['insp_date']);
		$history->setActionDate($row['insp_date']);
		$history->setNotes("$row[action_taken]\n$row[next_action]");
		if ($row['username']) {
			try {
				$person = new Person($row['username']);
				$history->setEnteredByPerson($person);
				$history->setActionPerson($person);
			}
			catch (Exception $e) {
				$person = new Person();
				$person->setUsername($row['username']);
				$person->setAuthenticationMethod('LDAP');

				try {
					$ldap = new LDAPEntry($person->getUsername());
					$person->setFirstname($ldap->getFirstname());
					$person->setLastname($ldap->getLastname());
					$person->setEmail($ldap->getEmail());

					$person->save();
				}
				catch (Exception $e) {
					$person = new Person();
					$person->setFirstname(strtolower($row['username']));
					$person->save();
				}
				$history->setEnteredByPerson($person);
				$history->setActionPerson($person);
			}
		}
		elseif ($row['needed'] || $row['existing']) {
			$name = $row['needed'] ? $row['needed'] : $row['existing'];
			list($firstname,$lastname) = explode(' ',$name);
			$list = new PersonList(array(
				'firstname'=>ucwords(strtolower($firstname)),
				'lastname'=>ucwords(strtolower($lastname))
			));
			if (count($list)) {
				$list->next();
				$history->setEnteredByPerson($list->current());
				$history->setActionPerson($list->current());
			}
		}
		else {
			$name = explode(' ',$row['inspector']);
			$search = array('firstname'=>ucwords(strtolower($name[0])));
			if (isset($name[1])) {
				$search['lastname'] = ucwords(strtolower($name[1]));
			}
			$list = new PersonList($search);
			if (count($list)) {
				$list->next();
				$history->setEnteredByPerson($list->current());
				$history->setActionPerson($list->current());
			}
		}
		if ($history->getActionPerson()) {
			$lastPerson = $history->getActionPerson();
		}
		try {
			$ticket->updateHistory($history);
		}
		catch (Exception $e) {
			// Any problems when creating the inspection, and we'll just not bother
			// to create it.  We're missing important information
			echo "Couldn't save inspection\n";
			echo $e->getMessage()."\n";
			print_r($history);
			exit();
		}

		if ($row['followup_date']) {
			$history = new History();
			$history->setAction('followup');
			$history->setActionDate($row['followup_date']);
			$history->setEnteredDate($row['followup_date']);
			$history->setNotes("$row[action_taken]\n$row[next_action]");
			if ($lastPerson) {
				try {
					$history->setEnteredByPerson($lastPerson);
					$history->setActionPerson($lastPerson);
				}
				catch (Exception $e) {
					echo "Could not set a person for the followup\n";
					print_r($ticket);
					print_r($lastPerson);
					exit();
				}
			}
			try {
				$ticket->updateHistory($history);
			}
			catch (Exception $e) {
				// Anything that doesn't save, we're just going to ignore
				// No sense bringing over bad data.
				echo "Couldn't save followup\n";
				echo $e->getMessage()."\n";
				print_r($history);
				exit();
			}
		}

		if ($row['completed_date']) {
			$history = new History();
			$history->setAction('close');
			$history->setActionDate($row['completed_date']);
			$history->setEnteredDate($row['completed_date']);
			if ($lastPerson) {
				$history->setEnteredByPerson($lastPerson);
				$history->setActionPerson($lastPerson);
			}
			$ticket->updateHistory($history);
		}
	}

	try {
		if (!$ticket->getEnteredByPerson()) {
			$ticket->setEnteredByPerson('unknown');
		}
		if (!$ticket->getAssignedPerson()) {
			$ticket->setAssignedPerson('unknown');
		}
		$ticket->save();
	}
	catch (Exception $e) {
		echo $e->getMessage()."\n";
		print_r($e);
		exit();
	}
}
