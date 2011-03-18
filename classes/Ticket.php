<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Ticket
{
	private $id;
	private $enteredDate;
	private $enteredByPerson_id;
	private $assignedPerson_id;
	private $referredPerson_id;
	private $status;           // open, closed,
	private $resolution_id;
	private $location;
	private $street_address_id;
	private $subunit_id;
	private $neighborhoodAssociation;
	private $township;
	private $latitude;
	private $longitude;

	private $enteredByPerson;
	private $assignedPerson;
	private $referredPerson;
	private $resolution;

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
				$zend_db = Database::getConnection();
				$sql = 'select * from tickets where id=?';
				$result = $zend_db->fetchRow($sql,array($id));
			}

			if ($result) {
				foreach ($result as $field=>$value) {
					if ($value) {
						if (preg_match('/Date/',$field)) {
							$value = new Date($value);
						}
						$this->$field = $value;
					}
				}
			}
			else {
				throw new Exception('tickets/unknownTicket');
			}
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

		$data = array();
		$data['enteredDate'] = $this->enteredDate->format('Y-m-d');
		$data['enteredByPerson_id'] = $this->enteredByPerson_id ? $this->enteredByPerson_id : null;
		$data['assignedPerson_id'] = $this->assignedPerson_id ? $this->assignedPerson_id : null;
		$data['referredPerson_id'] = $this->referredPerson_id ? $this->referredPerson_id : null;
		$data['status'] = $this->status;
		$data['resolution_id'] = $this->resolution_id ? $this->resolution_id : null;
		$data['location'] = $this->location ? $this->location : null;
		$data['street_address_id'] = $this->street_address_id ? $this->street_address_id : null;
		$data['subunit_id'] = $this->subunit_id ? $this->subunit_id : null;
		$data['neighborhoodAssociation'] = $this->neighborhoodAssociation ? $this->neighborhoodAssociation : null;
		$data['township'] = $this->township ? $this->township : null;
		$data['latitude'] = $this->latitude ? $this->latitude : null;
		$data['longitude'] = $this->longitude ? $this->longitude : null;

		if ($this->id) {
			$this->update($data);
		}
		else {
			$this->insert($data);
		}
	}

	private function update($data)
	{
		$zend_db = Database::getConnection();
		$zend_db->update('tickets',$data,"id='{$this->id}'");
	}

	private function insert($data)
	{
		$zend_db = Database::getConnection();
		$zend_db->insert('tickets',$data);
		$this->id = $zend_db->lastInsertId('tickets','id');
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Returns the date/time in the desired format
	 *
	 * Format is specified using PHP's date() syntax
	 * http://www.php.net/manual/en/function.date.php
	 * If no format is given, the Date object is returned
	 *
	 * @param string $format
	 * @return string|DateTime
	 */
	public function getEnteredDate($format=null)
	{
		if ($format && $this->enteredDate) {
			return $this->enteredDate->format($format);
		}
		else {
			return $this->enteredDate;
		}
	}

	/**
	 * @return int
	 */
	public function getEnteredByPerson_id()
	{
		return $this->enteredByPerson_id;
	}

	/**
	 * @return Person
	 */
	public function getEnteredByPerson()
	{
		if ($this->enteredByPerson_id) {
			if (!$this->enteredByPerson) {
				$this->enteredByPerson = new Person($this->enteredByPerson_id);
			}
			return $this->enteredByPerson;
		}
		return null;
	}

	/**
	 * @return int
	 */
	public function getAssignedPerson_id()
	{
		return $this->assignedPerson_id;
	}

	/**
	 * @return Person
	 */
	public function getAssignedPerson()
	{
		if ($this->assignedPerson_id) {
			if (!$this->assignedPerson) {
				$this->assignedPerson = new Person($this->assignedPerson_id);
			}
			return $this->assignedPerson;
		}
		return null;
	}

	/**
	 * @return int
	 */
	public function getReferredPerson_id()
	{
		return $this->referredPerson_id;
	}

	/**
	 * @return ReferredPerson
	 */
	public function getReferredPerson()
	{
		if ($this->referredPerson_id) {
			if (!$this->referredPerson) {
				$this->referredPerson = new Person($this->referredPerson_id);
			}
			return $this->referredPerson;
		}
		return null;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return int
	 */
	public function getResolution_id()
	{
		return $this->resolution_id;
	}

	/**
	 * @return Resolution
	 */
	public function getResolution()
	{
		if ($this->resolution_id) {
			if (!$this->resolution) {
				$this->resolution = new Resolution($this->resolution_id);
			}
			return $this->resolution;
		}
	}

	/**
	 * @return string
	 */
	public function getLocation()
	{
		return $this->location;
	}

	/**
	 * @return int
	 */
	public function getStreet_address_id()
	{
		return $this->street_address_id;
	}

	/**
	 * @return int
	 */
	public function getSubunit_id()
	{
		return $this->subunit_id;
	}

	/**
	 * @return string
	 */
	public function getNeighborhoodAssociation()
	{
		return $this->neighborhoodAssociation;
	}

	/**
	 * @return string
	 */
	public function getTownship()
	{
		return $this->township;
	}

	/**
	 * @return float
	 */
	public function getLatitude()
	{
		return $this->latitude;
	}

	/**
	 * @return float
	 */
	public function getLongitude()
	{
		return $this->longitude;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------

	/**
	 * Sets the date
	 *
	 * Date arrays should match arrays produced by getdate()
	 *
	 * Date string formats should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param int|string|array $date
	 */
	public function setEnteredDate($date)
	{
		if ($date instanceof Date) {
			$this->enteredDate = $date;
		}
		elseif ($date) {
			$this->enteredDate = new Date($date);
		}
		else {
			$this->enteredDate = null;
		}
	}

	/**
	 * @param int $int
	 */
	public function setEnteredByPerson_id($int)
	{
		$this->enteredByPerson = new Person($int);
		$this->enteredByPerson_id = $int;
	}

	/**
	 * @param Person $person
	 */
	public function setEnteredByPerson($person)
	{
		$this->enteredByPerson_id = $person->getId();
		$this->enteredByPerson = $person;
	}

	/**
	 * @param int $int
	 */
	public function setAssignedPerson_id($int)
	{
		$this->assignedPerson = new Person($int);
		$this->assignedPerson_id = $int;
	}

	/**
	 * @param Person $person
	 */
	public function setAssignedPerson($person)
	{
		$this->assignedPerson_id = $person->getId();
		$this->assignedPerson = $person;
	}

	/**
	 * @param int $int
	 */
	public function setReferredPerson_id($int)
	{
		$this->referredPerson = new Person($int);
		$this->referredPerson_id = $int;
	}

	/**
	 * @param Person $person
	 */
	public function setReferredPerson($person)
	{
		$this->referredPerson_id = $person->getId();
		$this->referredPerson = $person;
	}

	/**
	 * @param string $string
	 */
	public function setStatus($string)
	{
		$this->status = trim($string);
	}

	/**
	 * @param int $id
	 */
	public function setResolution_id($id)
	{
		$this->resolution = new Resolution($id);
		$this->resolution_id = $this->resolution->getId();

		$this->status = 'closed';
	}

	/**
	 * @param Resolution|string $resolution
	 */
	public function setResolution($resolution)
	{
		if (!$resolution instanceof Resolution) {
			$resolution = new Resolution($resolution);
		}
		$this->resolution_id = $resolution->getId();
		$this->resolution = $resolution;

		$this->status = 'closed';
	}

	/**
	 * @param string $string
	 */
	public function setLocation($string)
	{
		$this->location = trim($string);
	}

	/**
	 * @param int $int
	 */
	public function setStreet_address_id($int)
	{
		$this->street_address_id = (int)$int;
	}

	/**
	 * @param int $int
	 */
	public function setSubunit_id($int)
	{
		$this->subunit_id = (int)$int;
	}

	/**
	 * @param string $string
	 */
	public function setNeighborhoodAssociation($string)
	{
		$this->neighborhoodAssociation = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setTownship($string)
	{
		$this->township = trim($string);
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
	 * @return IssueList
	 */
	public function getIssues()
	{
		return new IssueList(array('ticket_id'=>$this->id));
	}

	/**
	 * @return CategoryList
	 */
	public function getCategories()
	{
		return new CategoryList(array('ticket_id'=>$this->id));
	}

	/**
	 * @return TicketHistoryList
	 */
	public function getHistory()
	{
		return new TicketHistoryList(array('ticket_id'=>$this->id));
	}
}
