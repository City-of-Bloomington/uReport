<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Action
{
	private $id;
	private $ticket_id;
	private $actionType_id;
	private $enteredDate;
	private $enteredByPerson_id;
	private $actionDate;
	private $actionPerson_id;
	private $notes;

	private $ticket;
	private $actionType;
	private $enteredByPerson;
	private $actionPerson;

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
				$sql = 'select * from actions where id=?';
				$result = $zend_db->fetchRow($sql,array($id));
			}

			if ($result) {
				foreach ($result as $field=>$value) {
					if ($value) {
						if (preg_match('/Date/',$field)) {
							if (substr($value,0,4) != '0000') {
								$value = new Date($value);
							}
						}
						$this->$field = $value;
					}
				}
			}
			else {
				throw new Exception('actions/unknownAction');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->enteredDate = new Date();
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->actionType_id || !$this->enteredByPerson_id) {
			throw new Exception('missingRequiredFields');
		}

		if (!$this->enteredDate) {
			$this->enteredDate = new Date();
		}

		if (!$this->actionDate) {
			$this->actionDate = new Date();
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
		$data['actionType_id'] = $this->actionType_id;
		$data['enteredDate'] = $this->enteredDate->format('Y-m-d');
		$data['enteredByPerson_id'] = $this->enteredByPerson_id;
		$data['actionDate'] = $this->actionDate->format('Y-m-d');
		$data['actionPerson_id'] = $this->actionPerson_id ? $this->actionPerson_id : null;
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
		$zend_db->update('actions',$data,"id='{$this->id}'");
	}

	private function insert($data)
	{
		$zend_db = Database::getConnection();
		$zend_db->insert('actions',$data);
		$this->id = $zend_db->lastInsertId('actions','id');
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
	 * @return int
	 */
	public function getActionType_id()
	{
		return $this->actionType_id;
	}

	/**
	 * @return ActionType
	 */
	public function getActionType()
	{
		if ($this->actionType_id) {
			if (!$this->actionType) {
				$this->actionType = new ActionType($this->actionType_id);
			}
			return $this->actionType;
		}
		return null;
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
	 * Returns the date/time in the desired format
	 *
	 * Format is specified using PHP's date() syntax
	 * http://www.php.net/manual/en/function.date.php
	 * If no format is given, the Date object is returned
	 *
	 * @param string $format
	 * @return string|DateTime
	 */
	public function getActionDate($format=null)
	{
		if ($format && $this->actionDate) {
			return $this->actionDate->format($format);
		}
		else {
			return $this->actionDate;
		}
	}

	/**
	 * @return int
	 */
	public function getActionPerson_id()
	{
		return $this->actionPerson_id;
	}

	/**
	 * @return Person
	 */
	public function getActionPerson()
	{
		if ($this->actionPerson_id) {
			if (!$this->actionPerson) {
				$this->actionPerson = new Person($this->actionPerson_id);
			}
			return $this->actionPerson;
		}
		return null;
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
	public function setTicket($ticket)
	{
		$this->ticket_id = $ticket->getId();
		$this->ticket = $ticket;
	}

	/**
	 * @param int $int
	 */
	public function setActionType_id($int)
	{
		$this->actionType = new ActionType($int);
		$this->actionType_id = $int;
	}

	/**
	 * @param ActionType|string $actionType
	 */
	public function setActionType($actionType)
	{
		if (!$actionType instanceof ActionType) {
			$actionType = new ActionType($actionType);
		}
		$this->actionType_id = $actionType->getId();
		$this->actionType = $actionType;
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
		if ($date) {
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
	 * Sets the date
	 *
	 * Date arrays should match arrays produced by getdate()
	 *
	 * Date string formats should be in something strtotime() understands
	 * http://www.php.net/manual/en/function.strtotime.php
	 *
	 * @param int|string|array $date
	 */
	public function setActionDate($date)
	{
		if ($date) {
			$this->actionDate = new Date($date);
		}
		else {
			$this->actionDate = null;
		}
	}

	/**
	 * @param int $int
	 */
	public function setActionPerson_id($int)
	{
		$this->actionPerson = new Person($int);
		$this->actionPerson_id = $int;
	}

	/**
	 * @param Person $person
	 */
	public function setActionPerson($person)
	{
		$this->actionPerson_id = $person->getId();
		$this->actionPerson = $person;
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
}
