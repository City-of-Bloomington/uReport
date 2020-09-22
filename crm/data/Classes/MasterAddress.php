<?php
/**
 * Address Service implementation for City of Bloomington's Master Address system
 *
 * @copyright 2020 City of Bloomington, Indiana
 * @license https://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Site\Classes;

use Application\Models\AddressService;

class MasterAddress implements AddressService
{
    const FIELD_NEIGHBORHOOD = 'neighborhoodAssociation';
    const FIELD_TOWNSHIP     = 'township';

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
    public static function customFieldDefinitions(): array
    {
        return [
            self::FIELD_NEIGHBORHOOD => [
                'description' => 'Neighborhood Association',
                'formElement' => 'select'
            ],
            self::FIELD_TOWNSHIP => [
                'description' => 'Township',
                'formElement' => 'select'
            ]
        ];
    }

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
	 */
	public static function getLocationData(string $location): array
	{
		$data     = [];
		$location = trim($location);
		$parse    = self::parseAddress($location);

		if ($parse && $location) {
            $url  = self::addressSearchUrl($location);
            $json = self::doJsonRequest($url);
            if (isset($json['locations'])) {
                $address = self::chooseMatch($json['locations'], $parse);
                if ($address) {
                    $data = array_merge(self::extractUreportData($address),
                                        self::getCustomFieldData($address['address_id']));
                }
            }
		}
		return $data;
	}

	/**
	 * @return array   Address data
	 */
	private static function chooseMatch(array $locations, array $parse): array
	{
        foreach ($locations as $a) {
            if (empty($parse['subunitIdentifier'])) {
                if (empty($a['subunit_id'])) { return $a; }
            }
            else {
                if (!empty($a['subunit_id'])) { return $a; }
            }
        }
	}

	public static function searchAddresses(string $query): array
	{
		$results = [];
        $url     = self::addressSearchUrl($query);
        $json    = self::doJsonRequest($url);
        if (isset($json['locations'])) {
            foreach ($json['locations'] as $address) {
                $data = self::extractUreportData($address);
                $results[$data['location']] = $data;
            }
        }
		return $results;
	}

	private static function addressSearchUrl(string $query): string
	{
        return ADDRESS_SERVICE_URL.'/locations?'.http_build_query(['format'=>'json','location'=>$query], '', ';');
	}

	public static function parseAddress(string $address): ?array
	{
        $url = ADDRESS_SERVICE_URL.'/addresses/parse?'.http_build_query(['format'=>'json', 'address'=>$address], '', ';');
        return self::doJsonRequest($url);
	}

	/**
	 * Translate Master Address fields into uReport ticket fields
	 * @param  array $address        The address json data
	 * @param  array $parsedAddress
	 * @return array
	 */
	private static function extractUreportData(array $address): array
	{
        return [
            'location'  => $address['streetAddress'],
            'addressId' => $address['address_id'   ],
            'city'      => $address['city'         ],
            'state'     => $address['state'        ],
            'zip'       => $address['zip'          ],
            'latitude'  => $address['latitude'     ],
            'longitude' => $address['longitude'    ]
        ];
	}

	private static function getCustomFieldData(int $address_id): array
	{
        $data = [];
        $url  = ADDRESS_SERVICE_URL."/addresses/$address_id?format=json";
        $json = self::doJsonRequest($url);
        if (isset($json['address'])) {
            if (isset($json['address']['township_name'])) {
                $data[self::FIELD_TOWNSHIP] = $json['address']['township_name'];
            }
            foreach ($json['purposes'] as $p) {
                if ($p['purpose_type'] == 'NEIGHBORHOOD ASSOCIATION') {
                    $data[self::FIELD_NEIGHBORHOOD] = $p['name'];
                    break;
                }
            }
        }
        return $data;
	}

	private static function get(string $url): ?string
	{
		$request = curl_init($url);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
		$res     = curl_exec($request);
		return $res ? $res : null;
	}

	private static function doJsonRequest(string $url): ?array
	{
        $res = self::get($url);
        if ($res) {
            return json_decode($res, true);
        }
        return null;
	}
}
