<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class AddressService
{
	/**
	 * Define custom form fields for dealing with AddressServiceCache data
	 *
	 * AddressServiceCache fields can be included in forms, such as search,
	 * and displayed with the rest of the ticket information.
	 *
	 * When the sytem displays ticket information, it will look at this
	 * array for any additional fields of information to display
	 *
	 * When the system draws a search form, a call will be made to get this
	 * description of any custom fields to include.
	 * The description will be used as the label
	 * The formElement controls what HTML form element to render.
	 *		If the formElement is "select", then a drop down will be created,
	 *		populated with all possible values from the addressServiceCache
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
	 * It's important to match $data fieldnames with Ticket fieldnames.
	 * When this data is given to a Ticket, any fields that have the same name as Ticket
	 * properties will update the appropriate Ticket property.
	 *
	 * @param string $location
	 * @return array
	 */
	public static function getLocationData($location)
	{
		$data = array();
		$location = trim($location);
		$parsedAddress = self::parseAddress($location);

		if (defined('ADDRESS_SERVICE') && $location && isset($parsedAddress->street_number)) {
			$url = new URL(ADDRESS_SERVICE.'/home.php');
			$url->queryType = 'address';
			$url->format = 'xml';
			$url->query = $location;

			$xml = new SimpleXMLElement($url,null,true);
			if (count($xml)==1) {
				$data = self::extractAddressData($xml->address,$parsedAddress);
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

			$xml = new SimpleXMLElement($url,null,true);
			foreach ($xml as $address) {
				$data = self::extractAddressData($address,self::parseAddress($query));
				$results[$data['location']] = $data;
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

	/**
	 * @param SimpleXMLElement $address The address node
	 * @param array $parsedAddress
	 * @return array
	 */
	private static function extractAddressData($address,$parsedAddress)
	{
		$data = array();
		$data['location'] = "{$address->streetAddress}";
		$data['address_id'] = "{$address->id}";
		$data['city'] = "{$address->city}";
		$data['state'] = "{$address->state}";
		$data['zip'] = "{$address->zip}";
		$data['latitude'] = "{$address->latitude}";
		$data['longitude'] = "{$address->longitude}";
		$data['township'] = "{$address->township}";

		// See if there's a neighborhood association
		$neighborhood = $address->xpath("//purpose[@type='NEIGHBORHOOD ASSOCIATION']");
		if ($neighborhood) {
			$data['neighborhoodAssociation'] = "{$neighborhood[0]}";
		}

		// See if this is a subunit
		if ($parsedAddress->subunitIdentifier) {
			$subunit = $address->xpath("//subunit[identifier='{$parsedAddress->subunitIdentifier}']");
			if ($subunit) {
				$data['subunit_id'] = "{$subunit[0]['id']}";
				$data['location'] = "$data[location] {$subunit[0]->type} {$subunit[0]->identifier}";
			}
		}
		return $data;
	}
}
