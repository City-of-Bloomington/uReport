<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class IssueHistory extends History
{
	private $issue_id;
	private $contactMethod_id;

	private $issue;

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
			$this->actionDate = new Date();
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->issue_id || !$this->action_id) {
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
		$data['issue_id'] = $this->issue_id;
		$data['action_id'] = $this->action_id;
		$data['enteredDate'] = $this->enteredDate->format('Y-m-d');
		$data['enteredByPerson_id'] = $this->enteredByPerson_id ? $this->enteredByPerson_id : null;
		$data['actionDate'] = $this->actionDate->format('Y-m-d');
		$data['actionPerson_id'] = $this->actionPerson_id ? $this->actionPerson_id : null;
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
	public function getIssue_id()
	{
		return $this->issue_id;
	}

	/**
	 * @return Issue
	 */
	public function getIssue()
	{
		if ($this->issue_id) {
			if (!$this->issue) {
				$this->issue = new Issue($this->issue_id);
			}
			return $this->issue;
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

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------
	/**
	 * @param int $int
	 */
	public function setIssue_id($int)
	{
		$this->issue = new Issue($int);
		$this->issue_id = $int;
	}

	/**
	 * @param Issue $issue
	 */
	public function setIssue($issue)
	{
		$this->issue_id = $issue->getId();
		$this->issue = $issue;
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
	 * @param string|ContactMethod $method
	 */
	public function setContactMethod($method)
	{
		if (!$method instanceof ContactMethod) {
			$method = new ContactMethod($method);
		}
		$this->contactMethod_id = $method->getId();
		$this->contactMethod = $method;
	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * Returns an array of action strings
	 *
	 * Returns the distinct list of eventLabels that are used across all issues
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
