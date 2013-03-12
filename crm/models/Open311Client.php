<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Open311Client
{
	/**
	 * Translates an Open311 POST into a CRM POST
	 *
	 * A valid Client api_key is required in the POST
	 *
	 * http://wiki.open311.org/GeoReport_v2#POST_Service_Request
	 * Open311 POST Service Request: defines what key/values should
	 * be posted when adding a service request.
	 * However, we're just going to take the Open311 POST and
	 * hand it off to Ticket::handleAdd().  So, we have to
	 * translate all the Open311 POST parameters into the
	 * POST parameters that Ticket::handleAdd() expects.
	 *
	 * Media should not need any special handling here,
	 * since both CRM and Open311 use "media" as the fieldname
	 *
	 * @param array $open311Post The raw POST from the client
	 * @return array A POST for Ticket::handleAdd()
	 */
	public static function translatePostArray($open311Post)
	{
		// Make sure we have a valid api_key
		if (!empty($open311Post['api_key'])) { $client = Client::loadByApiKey($open311Post['api_key']); }
		else { throw new Exception('clients/unknownClient'); }

		$ticketPost = array(
			'client_id'       => $client->getId(),
			'contactMethod_id'=> $client->getContactMethod_id()
		);

		// The lookup table for keyname translations
		// 'open311Post_key'=>'ticketPost_key'
		$fields = array(
			// Ticket Fields
			'service_code'  =>'category_id',
			'lat'           =>'latitude',
			'long'          =>'longitude',
			'address_string'=>'location',
			// Issue Fields
			'description'   =>'description',
			'attribute'     =>'customFields'
		);
		foreach ($fields as $open311Field=>$crmField) {
			if (!empty($open311Post[$open311Field])) {
				$ticketPost[$crmField] = $open311Post[$open311Field];
			}
		}
		$person = self::findPerson($open311Post);
		if ($person) {
			$ticketPost['reportedByPerson_id'] = $person->getId();
		}

		return $ticketPost;
	}

	/**
	 * Try to find this person in the database
	 *
	 * @return Person
	 */
	public static function findPerson($post)
	{
		$search = array();

		$fields = array(
			'first_name'=> 'firstname',
			'last_name' => 'lastname',
			'email'     => 'email',
			'phone'     => 'phoneNumber',
			'device_id' => 'phoneDeviceId'
		);
		foreach ($fields as $open311Field=>$crmField) {
			if (!empty($post[$open311Field])) { $search[$crmField] = $post[$open311Field]; }
		}
		if (count($search)) {
			$list = new PersonList($search);
			if (count($list) == 1) { $person = $list[0]; }
			else {
				$p = array();
				foreach ($fields as $key=>$field) {
					if (!empty($post[$key])) { $p[$field] = $post[$key]; }
				}
				if (count($p)) {
					$person = new Person();
					try {
						$person->handleUpdate($p);
						$person->save();
					}
					catch (Exception $e) { unset($person); }
				}
			}
		}
		return isset($person) ? $person : null;
	}
}