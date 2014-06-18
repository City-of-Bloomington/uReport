<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Blossom\Classes\Database;

class Location
{
	/**
	 * Does different searches depending on the indexes of the $query
	 *
	 * @query['address'] Will do a search in CRM and a search for addresses in Master Address
	 * @query['street'] Will do a search in CRM and a search for streets in Master Address
	 * @query['text'] Will only do a search in CRM
	 *
	 * Addresses will be return as $array['location text'] = $flag
	 * where $flag is true if returned by AddressService
	 *
	 * @param array $query
	 * @return array
	 */
	public static function search($query)
	{
		$results = array();
		$crm_query = '';

		if (isset($query['address']) && $query['address']) {
			$crm_query = $query['address'];
		}
		elseif (isset($query['text']) && $query['text']) {
			$crm_query = $query['text'];
		}

		if ($crm_query) {
			$zend_db = Database::getConnection();
			$sql = "select location, addressId, city,
					'database' as source,
					count(*) as ticketCount
					from tickets where location like ?
					group by location, addressId, city";
			$q = $zend_db->fetchAll($sql, array("$crm_query%"));
			foreach ($q as $row) {
				$results[$row['location']] = $row;
			}

		}

		if (isset($query['address']) && $query['address']) {
			foreach (AddressService::searchAddresses($query['address']) as $location=>$data) {
				if (!isset($results[$location])) {
					$results[$location] = $data;
				}
				else {
					$results[$location] = array_merge($results[$location],$data);
				}
				$results[$location]['source'] = 'Master Address';
			}
		}
		elseif (isset($query['street']) && $query['street']) {
			foreach (AddressService::searchStreets($query['street']) as $location=>$street_id) {
				$results[$location]['addressId'] = $street_id;
				$results[$location]['source'] = 'Master Address';
			}
		}

		return $results;
	}
}
