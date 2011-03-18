<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class TicketHistory extends History
{
	private $ticket_id;

	private $ticket;

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
				$sql = 'select * from ticketHistory where id=?';
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
				throw new Exception('ticketHistory/unknownTicketHistory');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->enteredDate = new Date();
			$this->actionDate = new Date();
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 *
	 * Setting $preliminary will make the validation ignore the Ticket_id.
	 * This is usefull for validing all the user-input data before assigning
	 * the issue to a Ticket.
	 *
	 * @param bool $preliminary
	 * @throws Exception $e
	 */
	public function validate($preliminary=false)
	{
		if (!$preliminary && !$this->ticket_id) {
			throw new Exception('missingTicket_id');
		}

		if (!$this->action_id) {
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
		$data['action_id'] = $this->action_id;
		$data['enteredDate'] = $this->enteredDate->format('Y-m-d');
		$data['enteredByPerson_id'] = $this->enteredByPerson_id ? $this->enteredByPerson_id : null;
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
		$zend_db->update('ticketHistory',$data,"id='{$this->id}'");
	}

	private function insert($data)
	{
		$zend_db = Database::getConnection();
		$zend_db->insert('ticketHistory',$data);
		$this->id = $zend_db->lastInsertId('ticketHistory','id');
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------
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

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * Returns an array of status strings
	 *
	 * Returns the distinct list of statuses that are used across all tickets
	 *
	 * @return array
	 */
	public static function getStatuses()
	{
		$zend_db = Database::getConnection();
		$result = $zend_db->query('select distinct status from tickets');
		return $result->fetchAll(Zend_Db::FETCH_COLUMN);
	}

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
		$query = $zend_db->query('select distinct eventLabel from ticketHistory order by eventLabel');
		return $query->fetchAll(Zend_Db::FETCH_COLUMN);
	}
}
