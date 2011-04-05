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
		firstname,lastname,email,phone,address,city,state,zip		
		from contacts";

$result = $pdo->query($sql);

foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {

	$person = new Person();
	$person->setFirstname(ucwords(strtolower($row['firstname'])));
	$person->setPhone($row['phone']); 
	$person->setLastname(ucwords(strtolower($row['lastname'])));
	$person->setEmail($row['email']);
	 
	if ($row['address'] && (strtolower($row['city']) == 'bloomington')) {
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
			$person->setZip(substr(preg_replace('/[^0-9]/','',$row['zip']),0,5));
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