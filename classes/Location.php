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
	 * @query['subunit'] will do a specific query in Master Address for that subunit
	 * @query['street'] Will do a search in CRM and a search for streets in Master Address
	 * @query['text'] Will only do a search in CRM
	 *
	 * Addresses will be return as $array['location text'] = $id
	 * where $id is the Master Address id for that address.
	 * Not all results will have an $id in Master Address
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
			$sql = 'select distinct location,street_address_id from tickets where location like ? order by location';
			$r = $zend_db->query($sql,array("%$crm_query%"));
			foreach ($r->fetchAll() as $row) {
				$results[$row['location']] = $row['street_address_id'];
			}
		}

		if (isset($query['address']) && $query['address']) {
			$url = new URL(MASTER_ADDRESS.'/home.php');
			$url->queryType = 'address';
			$url->format = 'xml';
			$url->query = $query['address'];

			$xml = new SimpleXMLElement($url,null,true);
			foreach ($xml as $address) {
				$results["{$address->streetAddress}"] = "{$address->id}";
				if (isset($query['subunit_identifier']) && $query['subunit_identifier']) {
					$subunit = $address->xpath("//subunit[identifier='$query[subunit_identifier]']");
					if (count($subunit)) {
						$results["{$address->streetAddress} {$subunit[0]->type} {$subunit[0]->identifier}"] = "{$subunit[0]['id']}";
					}
				}
			}
		}
		if (isset($query['street']) && $query['street']) {
			$url = new URL(MASTER_ADDRESS.'/home.php');
			$url->queryType = 'street';
			$url->format = 'xml';
			$url->query = $query['street'];

			$xml = new SimpleXMLElement($url,null,true);
			foreach ($xml as $street) {
				$results["$street[name]"] = "$street[id]";
			}
		}
		return $results;
	}

	/**
	 * Returns all the various strings people have typed for a given location string
	 *
	 * @param string $location
	 * @return array
	 */
	public static function getNeighborhoodAssociations($location=null)
	{
		$zend_db = Database::getConnection();
		$sql = "select distinct neighborhoodAssociation
				from tickets
				where neighborhoodAssociation is not null";
		if ($location) {
			$sql.=" and location=?";
		}
		$r = $zend_db->query($sql,array($location));
		return $r->fetchAll(Zend_Db::FETCH_COLUMN);
	}

	/**
	 * @return array
	 */
	public static function getTownships()
	{
		$zend_db = Database::getConnection();
		$sql = 'select distinct township from tickets where township is not null';
		$r = $zend_db->query($sql);
		return $r->fetchAll(Zend_Db::FETCH_COLUMN);
	}
}
