<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../configuration.inc';
include '../../migrationConfig.inc';

$pdo = new PDO(MIGRATION_DSN,MIGRATION_USER,MIGRATION_PASS);

$sql = "select distinct
		first_name,middle_initial,last_name,
		address,home_phone,bus_phone,e_mail_address
		from ce_eng_comp
		where first_name is not null
		and last_name is not null";
$result = $pdo->query($sql);

foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {

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