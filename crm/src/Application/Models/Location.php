<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
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
     */
    public static function search(string $query): array
    {
        $results = [];
        $query   = trim($query);

        if (!empty($query)) {
            $sql = <<<END
            select location, addressId, city,
                  'database' as source,
                   count(*) as ticketCount
            from tickets
            where location like ?
            group by location, addressId, city
            END;
            $q = Database::query($sql, ["$query%"]);
            foreach ($q as $row) {
                $results[$row['location']] = $row;
            }

            if (defined('ADDRESS_SERVICE')) {
                $pdo = Database::getConnection();
                $sql = $pdo->prepare('select count(*) as ticketCount from tickets where addressId=?');
                $res = call_user_func(ADDRESS_SERVICE.'::searchAddresses', $query);
                foreach ($res as $location=>$data) {
                    if (!isset($results[$location])) {

                        $sql->execute([$data['addressId']]);
                        $r = $sql->fetchAll(\PDO::FETCH_ASSOC);
                        $data['ticketCount'] = $r[0]['ticketCount'];

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
