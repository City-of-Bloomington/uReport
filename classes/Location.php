<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Location
{
	public static function search($query,$includeExternalResults=false)
	{
		$results = array();

		$zend_db = Database::getConnection();
		$sql = 'select distinct location,street_address_id from tickets where location like ? order by location';
		$r = $zend_db->query($sql,array("%$query%"));
		foreach ($r->fetchAll() as $row) {
			$results[$row['location']] = $row['street_address_id'];
		}

		if ($includeExternalResults) {
			$url = new URL(MASTER_ADDRESS.'/home.php');
			$url->queryType = 'address';
			$url->format = 'xml';
			$url->query = $query;

			$xml = new SimpleXMLElement($url,null,true);
			foreach ($xml as $address) {
				$results["{$address->streetAddress}"] = "{$address->id}";
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
