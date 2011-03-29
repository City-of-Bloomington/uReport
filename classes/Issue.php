<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Issue
{
	private $id;
	private $date;
	private $ticket_id;
	private $issueType_id;
	private $reportedByPerson_id;
	private $contactMethod_id;
	private $responseMethod_id;
	private $enteredByPerson_id;
	private $notes;
	private $case_number;

	private $ticket;
	private $issueType;
	private $reportedByPerson;
	private $contactMethod;
	private $responseMethod;
	private $enteredByPerson;

	private $categories = array();

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
				$sql = 'select * from issues where id=?';
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
				throw new Exception('issues/unknownIssue');
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

		if (!$this->issueType_id) {
			throw new Exception('missingRequiredFields');
		}

		#if (!$this->enteredByPerson_id) {
		#	throw new Exception('missingRequiredFields');
		#}

		if (!$this->date) {
			$this->date = new Date();
		}
	}

	/**
	 * Saves this record back to the database
	 */
	public function save()
	{
		$this->validate();

		$data = array();
		$data['date'] = $this->getDate('Y-m-d');
		$data['ticket_id'] = $this->ticket_id;
		$data['issueType_id'] = $this->issueType_id;
		$data['reportedByPerson_id'] = $this->reportedByPerson_id ? $this->reportedByPerson_id : null;
		$data['contactMethod_id'] = $this->contactMethod_id ? $this->contactMethod_id : null;
		$data['responseMethod_id'] = $this->responseMethod_id ? $this->responseMethod_id : null;
		$data['enteredByPerson_id'] = $this->enteredByPerson_id;
		$data['notes'] = $this->notes ? $this->notes : null;
		$data['case_number'] = $this->case_number ? $this->case_number : null;

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
		$zend_db->update('issues',$data,"id='{$this->id}'");
	}

	private function insert($data)
	{
		$zend_db = Database::getConnection();
		$zend_db->insert('issues',$data);
		$this->id = $zend_db->lastInsertId('issues','id');
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
	public function getIssueType_id()
	{
		return $this->issueType_id;
	}

	/**
	 * @return IssueType
	 */
	public function getIssueType()
	{
		if ($this->issueType_id) {
			if (!$this->issueType) {
				$this->issueType = new IssueType($this->issueType_id);
			}
			return $this->issueType;
		}
		return null;
	}

	/**
	 * @return int
	 */
	public function getReportedByPerson_id()
	{
		return $this->reportedByPerson_id;
	}

	/**
	 * @return Person
	 */
	public function getReportedByPerson()
	{
		if ($this->reportedByPerson_id) {
			if (!$this->reportedByPerson) {
				$this->reportedByPerson = new Person($this->reportedByPerson_id);
			}
			return $this->reportedByPerson;
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
		return null;
	}

	/**
	 * @return int
	 */
	public function getResponseMethod_id()
	{
		return $this->responseMethod_id;
	}

	/**
	 * @return ContactMethod
	 */
	public function getResponseMethod()
	{
		if ($this->responseMethod_id) {
			if (!$this->responseMethod) {
				$this->responseMethod = new ContactMethod($this->responseMethod_id);
			}
			return $this->responseMethod;
		}
		return null;
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
	 * @return text
	 */
	public function getNotes()
	{
		return $this->notes;
	}

	/**
	 * @return string
	 */
	public function getCase_number()
	{
		return $this->case_number;
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
	public function setTicket(Ticket $ticket)
	{
		$this->ticket_id = $ticket->getId();
		$this->ticket = $ticket;
	}

	/**
	 * @param int $int
	 */
	public function setIssueType_id($int)
	{
		$this->issueType = new IssueType($int);
		$this->issueType_id = $int;
	}

	/**
	 * @param string|IssueType $issueType
	 */
	public function setIssueType($issueType)
	{
		if ($issueType) {
			if (!$issueType instanceof IssueType) {
				$issueType = new IssueType($issueType);
			}
			$this->issueType_id = $issueType->getId();
			$this->issueType = $issueType;
		}
		else {
			$this->issueType = null;
			$this->issueType_id = null;
		}
	}

	/**
	 * @param int $int
	 */
	public function setReportedByPerson_id($int)
	{
		$this->reportedByPerson = new Person($int);
		$this->reportedByPerson_id = $int;
	}

	/**
	 * @param Person $person
	 */
	public function setReportedByPerson(Person $person)
	{
		$this->reportedByPerson_id = $person->getId();
		$this->reportedByPerson = $person;
	}

	/**
	 * @param int $int
	 */
	public function setContactMethod_id($int)
	{
		$this->contactMethod = new ContactMethod($int);
		$this->contactMethod_id = $int;
	}

	/**
	 * @param string|ContactMethod $contactMethod
	 */
	public function setContactMethod($contactMethod)
	{
		if ($contactMethod) {
			if (!$contactMethod instanceof ContactMethod) {
				$contactMethod = new ContactMethod($contactMethod);
			}
			$this->contactMethod_id = $contactMethod->getId();
			$this->contactMethod = $contactMethod;
		}
		else {
			$this->contactMethod = null;
			$this->contactMethod_id = null;
		}
	}

	/**
	 * @param int $int
	 */
	public function setResponseMethod_id($int)
	{
		$this->responseMethod = new ContactMethod($int);
		$this->responseMethod_id = $int;
	}

	/**
	 * @param string|ContactMethod $responseMethod
	 */
	public function setResponseMethod($responseMethod)
	{
		if ($responseMethod) {
			if (!$responseMethod instanceof ContactMethod) {
				$responseMethod = new ContactMethod($responseMethod);
			}
			$this->responseMethod_id = $responseMethod->getId();
			$this->responseMethod = $responseMethod;
		}
		else {
			$this->responseMethod = null;
			$this->responseMethod_id = null;
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
	public function setEnteredByPerson(Person $person)
	{
		$this->enteredByPerson_id = $person->getId();
		$this->enteredByPerson = $person;
	}

	/**
	 * @param text $text
	 */
	public function setNotes($text)
	{
		$this->notes = $text;
	}

	/**
	 * @param string $string
	 */
	public function setCase_number($string)
	{
		$this->case_number = trim($string);
	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return array
	 */
	public function getCategories()
	{
		if (!count($this->categories) && $this->id) {
			$categories = new CategoryList(array('issue_id'=>$this->id));
			foreach ($categories as $category) {
				$this->categories[$category->getId()] = $category;
			}
		}
		return $this->categories;
	}

	/**
	 * Saves a set of categories to the database
	 *
	 * Replaces the database records for the issue
	 * with a new set of categories
	 *
	 * @param string|array|CategoryList $categories
	 */
	public function saveCategories($categories)
	{
		if ($this->id) {
			$this->categories = array();
			$zend_db = Database::getConnection();
			$zend_db->delete('issue_categories','issue_id='.$this->id);

			if (is_string($categories)) {
				$categories = explode(',',$categories);
			}
			foreach ($categories as $category) {
				if (!$category instanceof Category) {
					$category = new Category($category);
				}
				if ($category->getId()) {
					$zend_db->insert('issue_categories',
								array('issue_id'=>$this->id,'category_id'=>$category->getId()));
				}
			}
		}
	}

	/**
	 * @param Category $category
	 * @return bool
	 */
	public function hasCategory(Category $category)
	{
		if (count($this->getCategories())) {
			return array_key_exists($category->getId(),$this->getCategories());
		}
	}

	/**
	 * @return bool
	 */
	public function hasCategories()
	{
		return count($this->getCategories()) ? true : false;
	}

	/**
	 * @return IssueHistoryList
	 */
	public function getHistory()
	{
		return new IssueHistoryList(array('issue_id'=>$this->id));
	}
}
