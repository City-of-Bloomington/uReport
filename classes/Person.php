<?php
/**
 * @copyright 2009-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Person
{
	private $data = array();

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
	 * @param int|string|array $id (ID, email, username)
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				echo "array passed in\n";
				$result = $id;
			}
			else {
				echo "trying to load from db\n";
				$mongo = Database::getConnection();
				if (is_numeric($id)) {
					$search = array('_id'=>new MongoId($id));
				}
				elseif (false !== strpos($id,'@')) {
					$search = array('email'=>$id);
				}
				else {
					$search = array('username'=>$id);
				}
				$result = $mongo->people->findOne($search);
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('people/unknownPerson');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		// Check for required fields here.  Throw an exception if anything is missing.
		if (!$this->data['firstname']
			&& !$this->data['lastname']
			&& !$this->data['organization']) {
			throw new Exception('missingRequiredFields');
		}
	}

	/**
	 * Saves this record back to the database
	 */
	public function save()
	{
		$this->validate();
		$mongo = Database::getConnection();
		try {
			$mongo->people->save($this->data,array('safe'=>true));
		}
		catch (Exception $e) {
			die($e->getMessage());
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
		if (isset($this->data['_id'])) {
			return $this->data['_id'];
		}
	}

	/**
	 * @return string
	 */
	public function getFirstname()
	{
		if (isset($this->data['firstname'])) {
			return $this->data['firstname'];
		}
	}

	/**
	 * @return string
	 */
	public function getMiddlename()
	{
		if (isset($this->data['middlename'])) {
			return $this->data['middlename'];
		}
	}

	/**
	 * @return string
	 */
	public function getLastname()
	{
		if (isset($this->data['lastname'])) {
			return $this->data['lastname'];
		}
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		if (isset($this->data['email'])) {
			return $this->data['email'];
		}
	}

	/**
	 * @return string
	 */
	public function getPhone()
	{
		if (isset($this->data['phone'])) {
			return $this->data['phone'];
		}
	}

	/**
	 * @return string
	 */
	public function getOrganization()
	{
		if (isset($this->data['organization'])) {
			return $this->data['organization'];
		}
	}

	/**
	 * @return string
	 */
	public function getAddress()
	{
		if (isset($this->data['address'])) {
			return $this->data['address'];
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

	public function getUsername()
	{
		if (isset($this->data['username'])) {
			return $this->data['username'];
		}
	}

	public function getAuthenticationMethod()
	{
		if (isset($this->data['authenticationMethod'])) {
			return $this->data['authenticationMethod'];
		}
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------
	/**
	 * @param string $string
	 */
	public function setFirstname($string)
	{
		$this->data['firstname'] = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setMiddlename($string)
	{
		$this->data['middlename'] = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setLastname($string)
	{
		$this->data['lastname'] = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setEmail($string)
	{
		$this->data['email'] = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setPhone($string)
	{
		$this->data['phone'] = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setOrganization($string)
	{
		$this->data['organization'] = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setAddress($string)
	{
		$this->data['address'] = trim($string);
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

	public function setUsername($string)
	{
		$this->data['username'] = trim($string);
	}

	public function setAuthenticationMethod($string)
	{
		$this->data['authenticationMethod'] = trim($string);
	}


	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getFullname()
	{
		if ($this->getFirstname() || $this->getLastname()) {
			return "{$this->getFirstname()} {$this->getLastname()}";
		}
		else {
			return $this->getOrganization();
		}
	}

	/**
	 * @return string
	 */
	public function getURL()
	{
		if ($this->getId()) {
			return BASE_URL.'/people/viewPerson.php?person_id='.$this->getId();
		}
	}

	/**
	 * @return Department
	 */
	public function getDepartment()
	{
		if (isset($this->data['department'])) {
			return $this->data['department'];
		}
	}

	/**
	 * @return TicketList
	 */
	public function getReportedTickets() {
		if ($this->id) {
			return new TicketList(array('reportedByPerson_id'=>$this->id));
		}
	}

	/**
	 * Transfers all data from a person, then deletes that person
	 *
	 * This person will end up containing all information from both people
	 *
	 * @param Person $person
	 */
	public function mergeFrom(Person $person)
	{
		if ($this->id && $person->getId()) {
			if($this->id == $person->getId()){
				//
				// can not merge same person throw exception
				throw new Exception('mergerNotAllowed');
			}
			if($this->getUser_id() || $person->getUser_id()){
				//
				// do not allow merger of two users with different userid's throw exception
				throw new Exception('mergerNotAllowed');
			}
			$zend_db = Database::getConnection();
			$zend_db->update('departments',array('default_person_id'=>$this->id),'default_person_id='.$person->getId());
			$zend_db->update('issueHistory',array('enteredByPerson_id'=>$this->id),'enteredByPerson_id='.$person->getId());
			$zend_db->update('issueHistory',array('actionPerson_id'=>$this->id),'actionPerson_id='.$person->getId());
			$zend_db->update('issues',array('enteredByPerson_id'=>$this->id),'enteredByPerson_id='.$person->getId());
			$zend_db->update('issues',array('reportedByPerson_id'=>$this->id),'reportedByPerson_id='.$person->getId());
			$zend_db->update('ticketHistory',array('enteredByPerson_id'=>$this->id),'enteredByPerson_id='.$person->getId());
			$zend_db->update('ticketHistory',array('actionPerson_id'=>$this->id),'actionPerson_id='.$person->getId());
			$zend_db->update('tickets',array('enteredByPerson_id'=>$this->id),'enteredByPerson_id='.$person->getId());
			$zend_db->update('tickets',array('assignedPerson_id'=>$this->id),'assignedPerson_id='.$person->getId());
			$zend_db->update('tickets',array('referredPerson_id'=>$this->id),'referredPerson_id='.$person->getId());
			$zend_db->delete('people','id='.$person->getId());
		}
	}
}
