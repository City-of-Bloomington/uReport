<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Ticket
{
	private $id;
	private $date;
	private $person_id;
	private $location;
	private $street_address_id;
	private $subunit_id;
	private $neighborhoodAssociation;
	private $township;

	private $person;

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
						if ($field=='date') {
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
			$this->date = new Date();
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		#if (!$this->person_id) {
		#	throw new Exception('tickets/missingPerson');
		#}

	}

	/**
	 * Saves this record back to the database
	 */
	public function save()
	{
		$this->validate();

		$data = array();
		$data['date'] = $this->date->format('Y-m-d');
		$data['person_id'] = $this->person_id;
		$data['location'] = $this->location ? $this->location : null;
		$data['street_address_id'] = $this->street_address_id ? $this->street_address_id : null;
		$data['subunit_id'] = $this->subunit_id ? $this->subunit_id : null;
		$data['neighborhoodAssociation'] = $this->neighborhoodAssociation ? $this->neighborhoodAssociation : null;
		$data['township'] = $this->township ? $this->township : null;

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
	public function getDate($format=null)
	{
		if ($format && $this->date) {
			return $this->date->format($format);
		}
		else {
			return $this->date;
		}
	}

	/**
	 * @return int
	 */
	public function getPerson_id()
	{
		return $this->person_id;
	}

	/**
	 * @return Person
	 */
	public function getPerson()
	{
		if ($this->person_id) {
			if (!$this->person) {
				$this->person = new Person($this->person_id);
			}
			return $this->person;
		}
		return null;
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
	public function setDate($date)
	{
		if ($date) {
			$this->date = new Date($date);
		}
		else {
			$this->date = null;
		}
	}

	/**
	 * @param int $int
	 */
	public function setPerson_id($int)
	{
		$this->person = new Person($int);
		$this->person_id = $int;
	}

	/**
	 * @param Person $person
	 */
	public function setPerson($person)
	{
		$this->person_id = $person->getId();
		$this->person = $person;
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

	public function getCategories()
	{
		return new CategoryList(array('ticket_id'=>$this->id));
	}

	/**
	 * Used only for importing data
	 * @param int $int
	 */
	public function setId($id)
	{
		if (!$this->id) {
			// Make sure we're not duplicating an ID
			try {
				$ticket = new Ticket($id);
			}
			catch (Exception $e) {
				$this->id = (int)$id;

				$data = array();
				$data['id'] = $this->id;
				$data['date'] = $this->date->format('Y-m-d');
				$data['person_id'] = $this->person_id ? $this->person_id : null;
				$data['location'] = $this->location ? $this->location : null;
				$data['street_address_id'] = $this->street_address_id ? $this->street_address_id : null;
				$data['subunit_id'] = $this->subunit_id ? $this->subunit_id : null;

				$zend_db = Database::getConnection();
				$zend_db->insert('tickets',$data);
				return;
			}
			throw new Exception('tickets/duplicateID');
		}
	}
}
