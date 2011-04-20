<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Ticket
{
	private $data = array();
	
	// Used to identify fields that can be updated from the AddressService
	private	$addressServiceFields = array(
		'location','address_id','city','state','zip','latitude','longitude'
	);

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

			$this->addressServiceCache = AddressService::getTicketData($this);
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->enteredDate = new Date();
			$this->status = 'open';
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->status) {
			$this->status = 'open';
		}

		if (!$this->enteredDate) {
			$this->enteredDate = new Date();
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
		if ($format) {
			list($microseconds,$timestamp) = explode(' ',$this->data['enteredDate']);
			return date($format,$timestamp);
		}
		else {
			return $this->date['enteredDate'];
		}
	}
	
	/**
	 * @return array
	 */
	public function getEnteredByPerson()
	{
		return $this->data['enteredByPerson'];
	}

	/**
	 * @return array
	 */
	public function getAssignedPerson()
	{
		return $this->data['assignedPerson'];
	}

	/**
	 * @return array
	 */
	public function getReferredPerson()
	{
		return $this->data['referredPerson'];
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->data['status'];
	}

	/**
	 * @return string
	 */
	public function getResolution()
	{
		return $this->data['resolution'];
	}

	/**
	 * @return string
	 */
	public function getLocation()
	{
		return $this->data['location'];
	}

	/**
	 * @return float
	 */
	public function getLatitude()
	{
		return $this->data['latitude'];
	}

	/**
	 * @return float
	 */
	public function getLongitude()
	{
		return $this->data['longitude'];
	}

	/**
	 * @return int
	 */
	public function getAddress_id()
	{
		return $this->data['address_id'];
	}

	/**
	 * @return string
	 */
	public function getCity()
	{
		return $this->data['city'];
	}

	/**
	 * @return string
	 */
	public function getState()
	{
		return $this->data['state'];
	}

	/**
	 * @return string
	 */
	public function getZip()
	{
		return $this->data['zip'];
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
	 * @param string $id
	 */
	public function setEnteredByPerson($id)
	{
		$person = new Person($id);
		$this->data['enteredByPerson'] = array(
			'_id'=>$person->getId(),
			'fullname'=>$person-getFullname()
		);
	}

	/**
	 * @param string $id
	 */
	public function setAssignedPerson($id)
	{
		$person = new Person($id);
		$this->data['assignedPerson'] = array(
			'_id'=>$person->getId(),
			'fullname'=>$person-getFullname()
		);
	}

	/**
	 * @param string $id
	 */
	public function setReferredPerson($id)
	{
		$person = new Person($id);
		$this->data['referredPerson'] = array(
			'_id'=>$person->getId(),
			'fullname'=>$person-getFullname()
		);
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
		$this->location = trim($string);
	}

	/**
	 * @param float $float
	 */
	public function setLatitude($float)
	{
		$this->latitude = (float)$float;
	}

	/**
	 * @param float $float
	 */
	public function setLongitude($float)
	{
		$this->longitude = (float)$float;
	}

	/**
	 * @param int $id
	 */
	public function setAddress_id($id)
	{
		$this->address_id = (int)$id;
	}

	/**
	 * @param string $string
	 */
	public function setCity($string)
	{
		$this->city = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setState($string)
	{
		$this->state = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setZip($string)
	{
		$this->zip = trim($string);
	}

	/**
	 * @return array
	 */
	public function getIssues()
	{
		return $this->data['issues'];
	}
	
	/**
	 * @return array
	 */
	public function getHistory()
	{
		return $this->data['history'];
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
		return BASE_URL.'/tickets/viewTicket.php?ticket_id='.$this->id;
	}


	/**
	 * @return array
	 */
	public function getCategories()
	{
		$categories = array();
		foreach ($this->data['issues'] as $issue) {
			$categories[] = $issue['category'];
		}
		return $categories;
	}

	/**
	 * Transfers all data from a ticket, then deletes the ticket
	 *
	 * This ticket will end up containing all information from both tickets
	 *
	 * @param Ticket $ticket
	 */
	public function mergeFrom(Ticket $ticket)
	{

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
		return $mongo->command(array('distinct'=>'tickets','key'=>$fieldname));
	}
}
