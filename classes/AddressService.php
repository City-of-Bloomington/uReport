<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class AddressService
{
	/**
	 * Define custom form fields for dealing with AddressServiceCache data
	 *
	 * AddressServiceCache fields can be included in forms, such as search.
	 * When the system draws a search form, a call will be made to get this
	 * description of any custom fields to include.
	 * The description will be used as the label
	 * The formElement controls what HTML form element to render.
	 *		If select, then a drop down will be created, populated with all
	 *		possible values from the addressServiceCache
	 *
	 *		All other form elements will be rendered as a plain text input
	 */
	public static $customFieldDescriptions = array(
		'neighborhoodAssociation'=>array(
			'description'=>'Neighborhood Association',
			'formElement'=>'select'
		),
		'township'=>array(
			'description'=>'Township',
			'formElement'=>'select'
		)
	);

	/**
	 * Loads the data from your address service for the location
	 *
	 * This function is where you can add any extra, custom fields that this
	 * application will keep track of.
	 *
	 * Your address system should have only one entry matching the location string.
	 * If there is not exactly one entry, the returned data will be empty
	 * This can be used to see if the location string is a valid address
	 * in your address system
	 *
	 * It's important to match $data fieldnames with Case fieldnames.
	 * When this data is given to a Case, any fields that have the same name as Case
	 * properties will update the appropriate Case property.
	 *
	 * @param string $location
	 * @return array
	 */
	public static function getLocationData($location)
	{
		$data = array();
		$location = trim($location);
		$parsed = self::parseAddress($location);

		if (defined('ADDRESS_SERVICE') && $location && isset($parsed->street_number)) {
			$url = new URL(ADDRESS_SERVICE.'/home.php');
			$url->queryType = 'address';
			$url->format = 'xml';
			$url->query = $location;

			$xml = new SimpleXMLElement($url,null,true);
			if (count($xml)==1) {
				$data['location'] = "{$xml->address->streetAddress}";
				$data['address_id'] = "{$xml->address->id}";
				$data['city'] = "{$xml->address->city}";
				$data['state'] = "{$xml->address->state}";
				$data['zip'] = "{$xml->address->zip}";
				$data['latitude'] = "{$xml->address->latitude}";
				$data['longitude'] = "{$xml->address->longitude}";
				$data['township'] = "{$xml->address->township}";

				// See if there's a neighborhood association
				$neighborhood = $xml->xpath("//purpose[@type='NEIGHBORHOOD ASSOCIATION']");
				if ($neighborhood) {
					$data['neighborhoodAssociation'] = "{$neighborhood[0]}";
				}

				// See if this is a subunit
				if ($parsed->subunitIdentifier) {
					$subunit = $xml->xpath("//subunit[identifier='{$parsed->subunitIdentifier}']");
					if ($subunit) {
						$data['subunit_id'] = "{$subunit[0]['id']}";
						$data['location'] = "$data[location] {$subunit[0]->type} {$subunit[0]->identifier}";
					}
				}
			}
		}

		return $data;
	}

	/**
	 * @param string $query
	 * @return array
	 */
	public static function searchAddresses($query)
	{
		$results = array();
		if (defined('ADDRESS_SERVICE')) {
			$url = new URL(ADDRESS_SERVICE.'/home.php');
			$url->queryType = 'address';
			$url->format = 'xml';
			$url->query = $query;

			$parsed = self::parseAddress($query);

			$xml = new SimpleXMLElement($url,null,true);
			foreach ($xml as $address) {
				$results["{$address->streetAddress}"] = "{$address->id}";

				if ($parsed->subunitIdentifier) {
					$upper = strtoupper($parsed->subunitIdentifier);
					$lower = strtolower($parsed->subunitIdentifier);
					$subunit = $address->xpath("//subunit[identifier='$upper' or identifier='$lower']");
					if (count($subunit)) {
						$results["{$address->streetAddress} {$subunit[0]->type} {$subunit[0]->identifier}"] = "{$subunit[0]['id']}";
					}
				}
			}
		}
		return $results;
	}

	/**
	 * @param string $query
	 * @return array
	 */
	public static function searchStreets($query)
	{
		$results = array();
		if (defined('ADDRESS_SERVICE')) {
			$url = new URL(ADDRESS_SERVICE.'/home.php');
			$url->queryType = 'street';
			$url->format = 'xml';
			$url->query = $query;

			$xml = new SimpleXMLElement($url,null,true);
			foreach ($xml as $street) {
				$results["$street[name]"] = "$street[id]";
			}
		}
		return $results;
	}

	/**
	 * @param string $address
	 * @return SimpleXMLElement
	 */
	public static function parseAddress($address)
	{
		if (defined('ADDRESS_SERVICE')) {
			$url = new URL(ADDRESS_SERVICE.'/addresses/parse.php');
			$url->format = 'xml';
			$url->address = $address;
			return new SimpleXMLElement($url,null,true);
		}
	}
}
