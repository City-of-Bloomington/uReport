<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Open311Client
{
	/**
	 * Translates Open311 fieldnames for a POST into CRM fieldnames
	 *
	 * A valid Client api_key is required in the POST
	 *
	 * Media should not need any special handling here,
	 * since both CRM and Open311 use "media" as the fieldname
	 *
	 * @param array $post The raw POST from the client
	 * @return array
	 */
	public static function translatePostArray($post)
	{
		// Make sure we have a valid api_key
		if (!empty($post['api_key'])) { $client = new Client($post['api_key']); }
		else { throw new Exception('clients/unknownClient'); }

		$p = array('client_id'=>$client->getId());

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
			if (!empty($post[$open311Field])) { $p[$crmField] = $post[$open311Field]; }
		}
		$person = self::findPerson($post);
		if ($person) { $p['reportedByPerson_id'] = $person->getId(); }

		return $p;
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
				foreach ($personFields as $key=>$field) {
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