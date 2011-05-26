<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Ticket extends MongoRecord
{
	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$result = $id;
			}
			else {
				$mongo = Database::getConnection();
				$result = $mongo->tickets->findOne(array('_id'=>new MongoId($id)));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('tickets/unknownTicket');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->data['enteredDate'] = new MongoDate();
			$this->data['status'] = 'open';
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->data['status']) {
			$this->data['status'] = 'open';
		}

		if (!$this->data['enteredDate']) {
			$this->data['enteredDate'] = new MongoDate();
		}

		#if (!$this->enteredByPerson_id) {
		#	throw new Exception('tickets/missingEnteredByPerson');
		#}
	}

	/**
	 * Saves this record back to the database
	 */
	public function save()
	{
		$this->validate();
		$mongo = Database::getConnection();
		$mongo->tickets->save($this->data,array('safe'=>true));
	}

	/**
	 * Deletes this ticket from the database
	 */
	public function delete()
	{
		if ($this->getId()) {
			$mongo = Database::getConnection();
			$mongo->tickets->remove(array('_id'=>$this->getId()));
		}
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------
	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->data['_id'];
	}

	/**
	 * Returns the date/time in the desired format
	 *
	 * Format is specified using PHP's date() syntax
	 * http://www.php.net/manual/en/function.date.php
	 * If no format is given, the MongoDate object is returned
	 *
	 * @param string $format
	 * @return string|MongoDate
	 */
	public function getEnteredDate($format=null)
	{
		if (isset($this->data['enteredDate'])) {
			if ($format) {
				return date($format,$this->data['enteredDate']->sec);
			}
			else {
				return $this->data['enteredDate'];
			}
		}
	}

	/**
	 * @return array
	 */
	public function getEnteredByPerson()
	{
		if (isset($this->data['enteredByPerson'])) {
			return $this->data['enteredByPerson'];
		}
	}

	/**
	 * @return array
	 */
	public function getAssignedPerson()
	{
		if (isset($this->data['assignedPerson'])) {
			return $this->data['assignedPerson'];
		}
	}

	/**
	 * @return array
	 */
	public function getReferredPerson()
	{
		if (isset($this->data['referredPerson'])) {
			return $this->data['referredPerson'];
		}
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		if (isset($this->data['status'])) {
			return $this->data['status'];
		}
	}

	/**
	 * @return string
	 */
	public function getResolution()
	{
		if (isset($this->data['resolution'])) {
			return $this->data['resolution'];
		}
	}

	/**
	 * @return string
	 */
	public function getLocation()
	{
		if (isset($this->data['location'])) {
			return $this->data['location'];
		}
	}

	/**
	 * @return float
	 */
	public function getLatitude()
	{
		if (isset($this->data['coordinates']['latitude'])) {
			return $this->data['coordinates']['latitude'];
		}
	}

	/**
	 * @return float
	 */
	public function getLongitude()
	{
		if (isset($this->data['coordinates']['longitude'])) {
			return $this->data['coordinates']['longitude'];
		}
	}

	/**
	 * @return int
	 */
	public function getAddress_id()
	{
		if (isset($this->data['address_id'])) {
			return $this->data['address_id'];
		}
	}

	/**
	 * @return string
	 */
	public function getCity()
	{
		if (isset($this->data['city'])) {
			return $this->data['city'];
		}
	}

	/**
	 * @return string
	 */
	public function getState()
	{
		if (isset($this->data['state'])) {
			return $this->data['state'];
		}
	}

	/**
	 * @return string
	 */
	public function getZip()
	{
		if (isset($this->data['zip'])) {
			return $this->data['zip'];
		}
	}

	/**
	 * @return array
	 */
	public function getIssues()
	{
		$issues = array();
		if (isset($this->data['issues'])) {
			foreach ($this->data['issues'] as $data) {
				$issues[] = new Issue($data);
			}
		}
		return $issues;
	}

	/**
	 * @return array
	 */
	public function getHistory()
	{
		$history = array();
		if (isset($this->data['history'])) {
			foreach ($this->data['history'] as $data) {
				$history[] = new History($data);
			}
		}
		return $history;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------
	/**
	 * Sets the date
	 *
	 * Dates should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param string $date
	 */
	public function setEnteredDate($date)
	{
		$date = trim($date);
		if ($date) {
			$this->data['enteredDate'] = new MongoDate(strtotime($date));
		}
	}

	/**
	 * Sets person data
	 *
	 * See: MongoRecord->setPersonData
	 *
	 * @param string|array|Person $person
	 */
	public function setEnteredByPerson($person)
	{
		if (is_string($person)) {
			$person = new Person($person);
		}
		elseif (is_array($person)) {
			$person = new Person($person['_id']);
		}

		if ($person instanceof Person) {
			if ($person->getUsername()) {
				$this->setPersonData('enteredByPerson',$person);
			}
			else {
				throw new Exception('tickets/personRequiresUsername');
			}
		}
	}

	/**
	 * Sets person data
	 *
	 * See: MongoRecord->setPersonData
	 *
	 * @param string|array|Person $person
	 */
	public function setAssignedPerson($person)
	{
		if (is_string($person)) {
			$person = new Person($person);
		}
		elseif (is_array($person)) {
			$person = new Person($person['_id']);
		}

		if ($person instanceof Person) {
			if ($person->getUsername()) {
				$this->setPersonData('assignedPerson',$person);
			}
			else {
				throw new Exception('tickets/personRequiresUsername');
			}
		}
	}

	/**
	 * Sets person data
	 *
	 * See: MongoRecord->setPersonData
	 *
	 * @param string|array|Person $person
	 */
	public function setReferredPerson($person)
	{
		$this->setPersonData('referredPerson',$person);
	}

	/**
	 * Sets the status and clears resolution, if necessary
	 *
	 * Setting status to anything other than closed will clear any previously set resolution
	 *
	 * @param string $string
	 */
	public function setStatus($string)
	{
		$this->data['status'] = trim($string);
		if ($this->data['status'] != 'closed') {
			unset($this->data['resolution']);
		}
	}

	/**
	 * @param string $resolution
	 */
	public function setResolution($resolution)
	{
		$resolution = trim($resolution);
		if ($resolution) {
			$this->data['resolution'] = $resolution;
		}
		elseif (isset($this->data['resolution'])) {
			unset($this->data['resolution']);
		}
		$this->data['status'] = 'closed';
	}

	/**
	 * @param string $string
	 */
	public function setLocation($string)
	{
		$this->data['location'] = trim($string);
	}

	/**
	 * @param float $float
	 */
	public function setLatitude($float)
	{
		$this->data['coordinates']['latitude'] = (float)$float;
	}

	/**
	 * @param float $float
	 */
	public function setLongitude($float)
	{
		$this->data['coordinates']['longitude'] = (float)$float;
	}

	/**
	 * @param int $id
	 */
	public function setAddress_id($id)
	{
		$this->data['address_id'] = (int)$id;
	}

	/**
	 * @param string $string
	 */
	public function setCity($string)
	{
		$this->data['city'] = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setState($string)
	{
		$this->data['state'] = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setZip($string)
	{
		$this->data['zip'] = trim($string);
	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getURL()
	{
		return BASE_URL."/tickets/viewTicket.php?ticket_id={$this->getId()}";
	}


	/**
	 * @return array
	 */
	public function getCategories()
	{
		$categories = array();
		foreach ($this->data['issues'] as $issue) {
			if (isset($issue['category'])) {
				$categories[] = $issue['category'];
			}
		}
		return $categories;
	}

	/**
	 * @param Issue $issue
	 * @param int $index
	 */
	public function updateIssues(Issue $issue, $index=null)
	{
		$issue->validate();

		if (!isset($this->data['issues'])) {
			$this->data['issues'] = array();
		}

		if (isset($index) && isset($this->data['issues'][$index])) {
			$this->data['issues'][$index] = $issue->getData();
		}
		else {
			$this->data['issues'][] = $issue->getData();
		}
	}

	/**
	 * @param History $history
	 * @param int $index
	 */
	public function updateHistory(History $history, $index=null)
	{
		$history->validate();

		if (!isset($this->data['history'])) {
			$this->data['history'] = array();
		}

		if (isset($index) && isset($this->data['history'][$index])) {
			$this->data['history'][$index] = $history->getData();
		}
		else {
			$this->data['history'][] = $history->getData();
		}
	}
	/**
	 * Transfers issues and history from another ticket into this one
	 *
	 * We're only migrating the issue and history
	 * Once we're done we delete the other ticket
	 *
	 * @param Ticket $ticket
	 */
	public function mergeFrom(Ticket $ticket)
	{
		foreach ($ticket->getIssues() as $issue) {
			$this->updateIssues($issue);
		}
		foreach ($ticket->getHistory() as $history) {
			$this->updateHistory($history);
		}

		$this->save();
		$ticket->delete();
	}

	/**
	 * Returns the array of distinct values used for Tickets in the system
	 *
	 * @param string $fieldname
	 * @return array
	 */
	public static function getDistinct($fieldname)
	{
		$mongo = Database::getConnection();
		$result = $mongo->command(array('distinct'=>'tickets','key'=>$fieldname));
		return $result['values'];
	}

	/**
	 * @param array $data
	 */
	public function setAddressServiceData($data)
	{
		foreach ($data as $key=>$value) {
			$set = 'set'.ucfirst($key);
			if (method_exists($this,$set)) {
				$this->$set($value);
			}
			else {
				$this->data[$key] = (string)$value;
			}
		}
	}
}
