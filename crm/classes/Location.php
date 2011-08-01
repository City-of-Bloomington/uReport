<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
			$crm_query = $query['address'];
		}
		// Don't do a mongo search if they're doing a street search
		// Mongo will return too many locations to be useful
		elseif (isset($query['text']) && $query['text']) {
			$crm_query = $query['text'];
		}

		if ($crm_query) {
			$mongo = Database::getConnection();

			$map = new MongoCode("function() {
				var address_id='',city='';
				if (this.address_id) {
					address_id = this.address_id;
				}
				if (this.city) {
					city = this.city;
				}
				emit(this.location,{count:1,address_id:address_id,city:city});
			}");

			$reduce = new MongoCode("function(key,values) {
				var result = { count:1,address_id:'',city:'' };

				for (var i in values) {
					result.count += values[i].count;
				}
				if (values[i].address_id) {
					result.address_id = values[i].address_id;
				}
				if (values[i].city) {
					result.city = values[i].city;
				}
				return result;
			}");

			$q = $mongo->command(array(
				'mapreduce'=>'tickets',
				'map'=>$map,
				'reduce'=>$reduce,
				'query'=>array('location'=>new MongoRegex("/$crm_query/i")),
				'out'=>array('inline'=>1)
			));

			foreach ($q['results'] as $location) {
				$results[$location['_id']] = array(
					'ticketCount'=>$location['value']['count'],
					'address_id'=>$location['value']['address_id'],
					'city'=>$location['value']['city'],
					'source'=>'mongo'
				);
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
				$results[$location]['address_id'] = $street_id;
				$results[$location]['source'] = 'Master Address';
			}
		}

		return $results;
	}
}
