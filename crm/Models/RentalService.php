<?php
/**
 * A class for retrieving data from the Rental Service
 *
 * The rental service is the web service interface to the rental application.
 * The rental application keeps track of information about rental properties.
 * The most important information for CRM is the Owner (or landlord) of the
 * property.
 *
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

class RentalService
{
	/**
	 * Returns an array of owner names for this location
	 *
	 * @param string $location
	 * @return array
	 */
	public static function getOwnerNames($location)
	{
		$output = array();

		$location = urlencode($location);
		$url = RENTAL_SERVICE."?streetAddress=$location&type=xml";
		$xml = simplexml_load_string(URL::get($url));
		$owners = $xml->xpath("//Owner");
		if (count($owners)) {
			foreach ($owners as $owner) {
				$output[] = $owner->Name;
			}
		}
		return $output;
	}
}
