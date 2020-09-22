<?php
/**
 * @copyright 2011-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;
use Application\Database;

class Location
{
	/**
	 * Searches local database and AddressService for location strings
	 *
	 * Locations will be return as $array['location text'] = $source
	 * where $source is the name of the AddressService
	 *
	 * @param string $query
	 * @return array
	 */
	public static function search($query)
	{
		$results = [];
		$query   = trim($query);

		if (!empty($query)) {
			$db = Database::getConnection();
			$sql = "select location, addressId, city,
					'database' as source,
					count(*) as ticketCount
					from tickets where location like ?
					group by location, addressId, city";
			$q = $db->query($sql)->execute(["$query%"]);
			foreach ($q as $row) {
				$results[$row['location']] = $row;
			}

			if (defined('ADDRESS_SERVICE')) {
                $sql = $db->createStatement('select count(*) as ticketCount from tickets where addressId=?');
                $res = call_user_func(ADDRESS_SERVICE.'::searchAddresses', $query);
                foreach ($res as $location=>$data) {
                    if (!isset($results[$location])) {

                        $r = $sql->execute([$data['addressId']]);
                        $t = $r->current();
                        $data['ticketCount'] = $t['ticketCount'];

                        $results[$location] = $data;
                    }
                    else {
                        $results[$location] = array_merge($results[$location],$data);
                    }
                    $results[$location]['source'] = 'Master Address';
                }
            }
		}

		return $results;
	}
}
