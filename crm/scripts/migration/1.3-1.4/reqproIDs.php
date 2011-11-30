<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';
include '../0.0-1.0/reqpro/migrationConfig.inc';
include '../0.0-1.0/reqpro/categoryTranslation.inc';

$FAILLOG = fopen('./matchFailed.log','w');

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
	echo "--------------------\n";
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
	if ($row['comments']) {
		$issue->setDescription($row['comments']);
	}
	$ticket->updateIssues($issue);


	$issue = $ticket->getIssue();
	$location = $ticket->getLocation();
	$date = $issue->getDate();
	$description = $issue->getDescription();

	echo "Query: $location, $description, $date\n";

	$ticketList = new TicketList();
	$ticketList->findByMongoQuery(array(
		'location'=>$location,
		'issues.description'=>$description,
		'issues.date'=>$date
	));

	if (count($ticketList) == 1) {
		// Update the Mongo case Number with what's in reqpro
		foreach ($ticketList as $t) {
			$data = $t->getData();
			$data['number'] = (int)$row['c_num'];
			echo "Saving new number $data[number]\n";
			#$mongo->tickets->save($data,array('safe'=>true));
		}
	}
	else {
		fwrite($FAILLOG,"$row[c_num]\n");
	}
}
