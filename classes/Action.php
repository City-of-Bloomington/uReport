<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Action
{
	private $id;
	private $actionType_id;
	private $date;
	private $ticket_id;
	private $person_id;
	private $targetPerson_id;
	private $notes;

	private $actionType;
	private $ticket;
	private $person;
	private $targetPerson;

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
						if ($field=='date') {
							if (substr($value,0,4)!='0000') {
								$value = new Date($value);
							}
							else {
								$value = new Date();
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
		if (!$this->actionType_id || !$this->ticket_id || !$this->person_id) {
			throw new Exception('missingRequiredFields');
		}

	}

	/**
	 * Saves this record back to the database
	 */
	public function save()
	{
		$this->validate();

		$data = array();
		$data['actionType_id'] = $this->actionType_id;
		$data['date'] = $this->date->format('Y-m-d');
		$data['ticket_id'] = $this->ticket_id;
		$data['person_id'] = $this->person_id;
		$data['targetPerson_id'] = $this->targetPerson_id ? $this->targetPerson_id : null;
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
	public function getTargetPerson_id()
	{
		return $this->targetPerson_id;
	}

	/**
	 * @return Person
	 */
	public function getTargetPerson()
	{
		if ($this->targetPerson_id) {
			if (!$this->targetPerson) {
				$this->targetPerson = new Person($this->targetPerson_id);
			}
			return $this->targetPerson;
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
	public function setActionType_id($int)
	{
		$this->actionType = new ActionType($int);
		$this->actionType_id = $int;
	}

	/**
	 * @param string|ActionType $actionType
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
	public function setDate($date)
	{
		if ($date instanceof Date) {
			$this->date = $date;
		}
		elseif ($date) {
			$this->date = new Date($date);
		}
		else {
			$this->date = null;
		}
	}

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
	 * @param int $int
	 */
	public function setTargetPerson_id($int)
	{
		$this->targetPerson = new Person($int);
		$this->targetPerson_id = $int;
	}

	/**
	 * @param Person $targetPerson
	 */
	public function setTargetPerson($targetPerson)
	{
		$this->targetPerson_id = $targetPerson->getId();
		$this->targetPerson = $targetPerson;
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
	 * Alias for getActionType()
	 *
	 * @return ActionType
	 */
	public function getType()
	{
		return $this->getActionType();
	}

	/**
	 * @return string
	 */
	public function getVerb()
	{
		return $this->getActionType()->getVerb();
	}
}
