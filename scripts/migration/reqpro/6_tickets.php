<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';
include './migrationConfig.inc';

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

$sql = "select c.*, t.comp_desc, a.name as neighborhood,
			i.username,i.needed,i.existing
		from ce_eng_comp c
		left join c_types t on c.c_type=t.c_type1
		left join comp_associations a on c.assoc_id=a.id
		left join inspectors i on c.inspector=i.inspector";
$result = $pdo->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

	$location = "$row[street_num] $row[street_dir] $row[street_name] $row[street_type] $row[sud_type] $row[sud_num]";
	$location = preg_replace('/\s+/',' ',$location);
	echo "$location ==> ";

	// Import the Dates
	$ticket = new Ticket();
	if ($row['received']) {
		$ticket->setDate($row['received']);
	}
	elseif ($row['completed_date']) {
		$ticket->setDate($row['completed_date']);
	}
	else {
		continue;
	}

	// Import the Person
	if (isset($row['received_by']) && $row['received_by']) {
		try {
			$user = new User(strtolower($row['received_by']));
			$ticket->setPerson($user->getPerson());
		}
		catch (Exception $e) {
		}
	}

	// Township
	if (isset($row['township']) && array_key_exists($row['township'],$townships)) {
		$ticket->setTownship($townships[$row['township']]);
	}

	// Neighborhood Association
	if (isset($row['neighborhood']) && $row['neighborhood']
		&& $row['neighborhood']!='Unspecified') {
		$ticket->setNeighborhoodAssociation($row['neighborhood']);
	}

	// Check the location against Master Address
	// Master Address data should overwrite information from ReqPro
	$row['street_num'] = preg_replace('/[^a-zA-Z0-9\-\&\s\'\/]/','',$row['street_num']);
	if ($row['street_num']) {
		$url = new URL(MASTER_ADDRESS.'/home.php');
		$url->queryType = 'address';
		$url->format = 'xml';

		$url->query = preg_match('/[a-zA-Z]/',$row['street_num'])
			? $row['street_num']
			: "$row[street_num] $row[street_dir] $row[street_name] $row[street_type] $row[sud_type] $row[sud_num]";

		echo $url->query.' ==> ';
		$xml = new SimpleXMLElement($url,null,true);

		if (count($xml)==1) {
			// Set the address
			$location = $xml->address->streetAddress;

			// See if there's a subunit
			$ticket->setStreet_address_id($xml->address->id);
			if ($row['sud_num']) {
				$subunit = $xml->xpath("//subunit[identifier='$row[sud_num]']");
				if ($subunit) {
					$ticket->setSubunit_id($subunit[0]['id']);
					$location.= " {$subunit[0]->type} {$subunit[0]->identifier}";
				}
			}
			$ticket->setLocation($location);

			// See if there's a neighborhood association
			$neighborhood = $xml->xpath("//purpose[@type='NEIGHBORHOOD ASSOCIATION']");
			if ($neighborhood) {
				$ticket->setNeighborhoodAssociation($neighborhood[0]);
			}

			$ticket->setTownship($xml->address->township);
			$ticket->setLatitude($xml->address->latitude);
			$ticket->setLongitude($xml->address->longitude);
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
	$ticket->save();


	// Create the issue on this ticket
	$issue = new Issue();
	$issue->setDate($ticket->getDate());
	$issue->setTicket($ticket);
	if ($ticket->getPerson()) {
		$issue->setPerson($ticket->getPerson());
	}
	$issue->setNotes($row['comments']);
	$issue->setCase_number($row['case_number']);
	$issue->setContactMethod($row['complaint_source']);

	if (preg_match('/COMPLAINT/',$row['comp_desc'])) {
		$issue->setIssueType('Complaint');
	}
	if (preg_match('/VIOLATION/',$row['comp_desc'])) {
		$issue->setIssueType('Violation');
	}
	else {
		$issue->setIssueType('Request');
	}

	$personList = new PersonList(array(
		'firstname'=>$row['first_name'],
		'middlename'=>$row['middle_initial'],
		'lastname'=>$row['last_name'],
		'email'=>$row['e_mail_address']
	));
	if (count($personList)) {
		$issue->setConstituent($personList[0]);
	}

	$issue->save();
	$category = trim($row['comp_desc']);
	if ($category) {
		$issue->saveCategories(array($category));
	}

	// Create the Action history
	if ($row['assigned_to']) {
		$action = new Action();
		$action->setActionType('assignment');
		$action->setActionDate($row['assigned_date']);
		$action->setTicket($ticket);
		$action->setEnteredDate($row['received']);
		$action->setNotes("$row[action_taken]\n$row[next_action]");
		try {
			$user = new User(strtolower($row['received_by']));
		}
		catch (Exception $e) {
			$user = new User('inghamn');
		}
		$action->setEnteredByPerson($user->getPerson());
		try {
			list($username,$fullname) = explode(':',$row['assigned_to']);
			$user = new User($username);
			$action->setActionPerson($user->getPerson());
			if ($ticket->getPerson()) {
				$action->setActionPerson($ticket->getPerson());
			}
			else {
				$action->setActionPerson($user->getPerson());
			}
			$action->save();
		}
		catch (Exception $e) {
			// Any problems with the action, and we won't save it
			// These problems should all be assignments to people we don't
			// have in the system
			if ($e->getMessage() != 'users/unknownUser') {
				echo $e->getMessage();
				echo "Problem saving assignment\n";
				print_r($action);
				exit();
			}
		}
	}

	if ($row['insp_date']) {
		$action = new Action();
		$action->setActionType('inspection');
		$action->setActionDate($row['insp_date']);
		$action->setTicket($ticket);
		$action->setEnteredDate($row['insp_date']);
		$action->setNotes("$row[action_taken]\n$row[next_action]");
		if ($row['username']) {
			try {
				$user = new User($row['username']);
				$action->setActionPerson($user->getPerson());
			}
			catch (Exception $e) {
				$user = new User();
				$user->setUsername($row['username']);
				$user->setAuthenticationMethod('LDAP');

				try {
					$person = new Person();
					$ldap = new LDAPEntry($user->getUsername());
					$person->setFirstname($ldap->getFirstname());
					$person->setLastname($ldap->getLastname());
					$person->setEmail($ldap->getEmail());

					$person->save();
					$user->setPerson($person);
					$user->save();
				}
				catch (Exception $e) {
					$person = new Person();
					$person->setFirstname($row['username']);
					$person->save();
				}
				$action->setActionPerson($person);
			}
		}
		elseif ($row['needed'] || $row['existing']) {
			$name = $row['needed'] ? $row['needed'] : $row['existing'];
			list($firstname,$lastname) = explode(' ',$name);
			$list = new PersonList(array('firstname'=>$firstname,'lastname'=>$lastname));
			if (count($list)) {
				$action->setActionPerson($list[0]);
			}
		}
		else {
			$name = explode(' ',$row['inspector']);
			$search = array('firstname'=>$name[0]);
			if (isset($name[1])) {
				$search['lastname'] = $name[1];
			}
			$list = new PersonList($search);
			if (count($list)) {
				$action->setActionPerson($list[0]);
			}
		}

		if ($action->getActionPerson()) {
			$action->setEnteredByPerson($action->getActionPerson());
		}
		elseif ($ticket->getPerson()) {
			$action->setEnteredByPerson($ticket->getPerson());
		}

		try {
			$action->save();
		}
		catch (Exception $e) {
			// Any problems when creating the inspection, and we'll just not bother
			// to create it.  We're missing important information
		}
	}
}