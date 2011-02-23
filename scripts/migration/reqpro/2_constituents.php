<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';
include './migrationConfig.inc';

$pdo = new PDO(MIGRATION_DSN,MIGRATION_USER,MIGRATION_PASS);

$sql = "select distinct
		first_name,middle_initial,last_name,
		home_phone,bus_phone,e_mail_address,
		address,city,state,zip_code
		from ce_eng_comp
		where first_name is not null
		and last_name is not null";
$result = $pdo->query($sql);

foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {
	// Skip people who are already in the system
	if ($row['e_mail_address']) {
		$list = new PersonList(array('email'=>$row['e_mail_address']));
		if (count($list)) {
			continue;
		}
	}
	$list = new PersonList(array('firstname'=>$row['first_name'],'lastname'=>$row['last_name']));
	if (count($list)) {
		continue;
	}

	$person = new Person();
	$person->setFirstname(ucwords(strtolower($row['first_name'])));
	$person->setMiddlename(ucwords(strtolower($row['middle_initial'])));
	$person->setLastname(ucwords(strtolower($row['last_name'])));
	$person->setEmail($row['e_mail_address']);
	$person->setPhone($row['home_phone'] ? $row['home_phone'] : $row['bus_phone']);

	if ($row['address']) {
		$row['address'] = preg_replace('/[^a-zA-Z0-9\-\&\s\'\/]/','',$row['address']);
		$url = new URL(MASTER_ADDRESS.'/addresses/parse.php');
		$url->format = 'xml';
		$url->address = $row['address'];
		$parsed = new SimpleXMLElement($url,null,true);
		if ($parsed->street_number && $parsed->street_name) {
			// Look up their address in Master Address
			$url = new URL(MASTER_ADDRESS.'/home.php');
			$url->queryType = 'address';
			$url->format = 'xml';
			$url->query = $row['address'];
			echo $url->query." ==> ";

			$xml = new SimpleXMLElement($url,null,true);
			if (count($xml)==1) {
				// Set the address
				$person->setAddress($xml->address->streetAddress);
				$person->setCity($xml->address->city);
				$person->setState($xml->address->state);
				$person->setZip($xml->address->zip);
				$person->setStreet_address_id($xml->address->id);

				// See if there's a subunit
				if ($parsed->subunitIdentifier) {
					$subunit = $xml->xpath("//subunit[identifier='{$parsed->subunitIdentifier}']");
					if ($subunit) {
						$person->setSubunit_id($subunit[0]['id']);
						$person->setAddress("{$person->getAddress()} {$subunit[0]->type} {$subunit[0]->identifier}");
					}
				}

				// See if there's a neighborhood association
				$neighborhood = $xml->xpath("//purpose[@type='NEIGHBORHOOD ASSOCIATION']");
				if ($neighborhood) {
					$person->setNeighborhoodAssociation($neighborhood[0]);
				}
				echo "{$person->getAddress()} ==>";
			}
		}

		if (!$person->getAddress()) {
			$person->setAddress(ucwords(strtolower($row['address'])));
			$person->setCity(ucwords(strtolower($row['city'])));
			$person->setState(substr(strtoupper($row['state']),0,2));
			$person->setZip(substr(preg_replace('/[^0-9]/','',$row['zip_code']),0,5));
		}
	}


	try {
		$person->save();
	}
	catch (Exception $e) {
		print_r($person);
		exit();
	}
	echo "{$person->getFullname()}\n";
}