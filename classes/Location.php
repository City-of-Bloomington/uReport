<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
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
			$crm_query = addslashes($query['address']);
		}
		#elseif (isset($query['street']) && $query['street']) {
		#	$crm_query = addslashes($query['street']);
		#}
		elseif (isset($query['text']) && $query['text']) {
			$crm_query = addslashes($query['text']);
		}

		if ($crm_query) {
			$zend_db = Database::getConnection();
			$sql = 'select distinct location,address_id from tickets where location like ? order by location';
			$r = $zend_db->query($sql,array("%$crm_query%"));
			foreach ($r->fetchAll() as $row) {
				$results[$row['location']] = $row['address_id'];
			}
		}

		if (isset($query['address']) && $query['address']) {
			$results = array_merge($results,AddressService::searchAddresses($query['address']));
		}

		if (isset($query['street']) && $query['street']) {
			$results = array_merge($results,AddressService::searchStreets($query['street']));
		}
		return $results;
	}
}
