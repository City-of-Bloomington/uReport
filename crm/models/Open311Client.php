<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Open311Client
{
	/**
	 * Translate Open311 fields into CRM fields
	 */
	public static $open311Fields = array(
		'ticketData'=>array(
			'lat'=>'latitude',
			'long'=>'longitude',
			'address_string'=>'location'
		),
		'issueData'=>array(
			'description'=>'description',
			'attribute'=>'customFields'
		)
	);
	public static $personFields = array(
		'first_name'=>'firstname',
		'last_name'=>'lastname',
		'email'=>'email',
		'phone'=>'phone.number',
		'device_id'=>'phone.device_id'
	);

	/**
	 * Creates a CRM Ticket from an Open311 client POST
	 *
	 * @param array $post The raw POST from the client
	 * @param Client $client
	 * @return Ticket
	 */
	public static function createTicket($post, Client $client=null)
	{
		$ticketData = array();
		$issueData = array();

		if ($client) {
			$ticketData['client_id'] = "{$client->getId()}";
		}

		if (!empty($post['service_code'])) {
			$category = new Category($post['service_code']);
		}
		else {
			throw new Exception('open311/missingServiceCode');
		}

		// Go through all the post fields and add them to the ticket
		foreach ($post as $key=>$value) {
			$value = is_string($value) ? trim($value) : $value;

			// Translate Open311 fields into CRM fields as we go
			if ($value) {
				if (isset(self::$open311Fields['ticketData'][$key])) {
					$ticketData[self::$open311Fields['ticketData'][$key]] = $value;
				}
				elseif (isset(self::$open311Fields['issueData'][$key])) {
					$issueData[self::$open311Fields['issueData'][$key]] = $value;
				}
			}
		}

		$ticket = new Ticket();
		$ticket->setCategory($category);
		$ticket->set($ticketData);

		$issue = new Issue();
		$issue->set($issueData);

		// Try to find this person in the database
		$search = array();
		foreach (self::$personFields as $open311Field=>$crmField) {
			if (!empty($post[$open311Field])) {
				$search[$crmField] = $post[$open311Field];
			}
		}
		if (count($search)) {
			$list = new PersonList($search);
			// If find exactly one person that matches, report the issue as that person
			if (count($list) == 1) {
				foreach ($list as $person) {
					$issue->setReportedByPerson($person);
				}
			}
			// Else add a new person with the info they gave us
			else {
				$person = new Person();
				foreach (self::$personFields as $key=>$field) {
					if (!empty($post[$key])) {
						$set = 'set'.ucfirst($field);
						$person->$set($post[$key]);
					}
				}
				try {
					$person->save();
					$issue->setReportedByPerson($person);
				}
				catch (Exception $e) {
					// Not sure if we should send an error message or not.
					// For now, just ignore
				}
			}
		}
		$ticket->updateIssues($issue);
		return $ticket;
	}
}