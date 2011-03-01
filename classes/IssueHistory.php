<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class IssueHistory
{
	private $id;
	private $ticket_id;
	private $eventLabel;
	private $eventDescription;
	private $enteredDate;
	private $eventDate;
	private $person_id;
	private $contactMethod_id;
	private $notes;

	private $ticket;
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
				$sql = 'select * from issueHistory where id=?';
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
				throw new Exception('issueHistory/unknownIssueHistory');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->enteredDate = new Date();
			$this->eventDate = new Date();
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->ticket_id || !$this->eventLabel || !$this->eventDescription) {
			throw new Exception('missingRequiredFields');
		}

		if (!$this->enteredDate) {
			$this->enteredDate = new Date();
		}

		if (!$this->eventDate) {
			$this->eventDate = new Date();
		}
	}

	/**
	 * Saves this record back to the database
	 */
	public function save()
	{
		$this->validate();

		$data = array();
		$data['ticket_id'] = $this->ticket_id;
		$data['eventLabel'] = $this->eventLabel;
		$data['eventDescription'] = $this->eventDescription;
		$data['enteredDate'] = $this->enteredDate->format('Y-m-d');
		$data['eventDate'] = $this->eventDate->format('Y-m-d');
		$data['person_id'] = $this->person_id ? $this->person_id : null;
		$data['contactMethod_id'] = $this->contactMethod_id ? $this->contactMethod_id : null;
		$data['notes'] = $this->notes ? $this->notes : null;

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
		$zend_db->update('issueHistory',$data,"id='{$this->id}'");
	}

	private function insert($data)
	{
		$zend_db = Database::getConnection();
		$zend_db->insert('issueHistory',$data);
		$this->id = $zend_db->lastInsertId('issueHistory','id');
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
	 * @return int
	 */
	public function getTicket_id()
	{
		return $this->ticket_id;
	}

	/**
	 * @return Ticket
	 */
	public function getTicket()
	{
		if ($this->ticket_id) {
			if (!$this->ticket) {
				$this->ticket = new Ticket($this->ticket_id);
			}
			return $this->ticket;
		}
		return null;
	}

	/**
	 * @return string
	 */
	public function getEventLabel()
	{
		return $this->eventLabel;
	}

	/**
	 * @return string
	 */
	public function getEventDescription()
	{
		return $this->eventDescription;
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
	 * Returns the date/time in the desired format
	 *
	 * Format is specified using PHP's date() syntax
	 * http://www.php.net/manual/en/function.date.php
	 * If no format is given, the Date object is returned
	 *
	 * @param string $format
	 * @return string|DateTime
	 */
	public function getEventDate($format=null)
	{
		if ($format && $this->eventDate) {
			return $this->eventDate->format($format);
		}
		else {
			return $this->eventDate;
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
	 * @return int
	 */
	public function getContactMethod_id()
	{
		return $this->contactMethod_id;
	}

	/**
	 * @return ContactMethod
	 */
	public function getContactMethod()
	{
		if ($this->contactMethod_id) {
			if (!$this->contactMethod) {
				$this->contactMethod = new ContactMethod($this->contactMethod_id);
			}
			return $this->contactMethod;
		}
	}

	/**
	 * @return text
	 */
	public function getNotes()
	{
		return $this->notes;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------

	/**
	 * @param int $int
	 */
	public function setTicket_id($int)
	{
		$this->ticket = new Ticket($int);
		$this->ticket_id = $int;
	}

	/**
	 * @param Ticket $ticket
	 */
	public function setTicket(Ticket $ticket)
	{
		$this->ticket_id = $ticket->getId();
		$this->ticket = $ticket;
	}

	/**
	 * @param string $string
	 */
	public function setEventLabel($string)
	{
		$this->eventLabel = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setEventDescription($string)
	{
		$this->eventDescription = trim($string);
	}

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
	 * Sets the date
	 *
	 * Date arrays should match arrays produced by getdate()
	 *
	 * Date string formats should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param int|string|array $date
	 */
	public function setEventDate($date)
	{
		if ($date instanceof Date) {
			$this->eventDate = $date;
		}
		elseif ($date) {
			$this->eventDate = new Date($date);
		}
		else {
			$this->eventDate = null;
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
	public function setPerson(Person $person)
	{
		$this->person_id = $person->getId();
		$this->person = $person;
	}

	/**
	 * @param int $id
	 */
	public function setContactMethod_id($id)
	{
		$this->contactMethod = new ContactMethod($id);
		$this->contactMethod_id = $this->contactMethod->getId();
	}

	/**
	 * @param ContactMethod $contactMethod
	 */
	public function setContactMethod(ContactMethod $contactMethod)
	{
		$this->contactMethod_id = $contactMethod->getId();
		$this->contactMethod = $contactMethod;
	}

	/**
	 * @param text $text
	 */
	public function setNotes($text)
	{
		$this->notes = $text;
	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * Returns an array of action strings
	 *
	 * Returns the distinct list of eventLabels that are used across all tickets
	 *
	 * @return array
	 */
	public static function getEventLabels()
	{
		$zend_db = Database::getConnection();
		$query = $zend_db->query('select distinct eventLabel from issueHistory order by eventLabel');
		return $query->fetchAll(Zend_Db::FETCH_COLUMN);
	}
}
