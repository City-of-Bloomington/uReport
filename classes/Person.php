<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Person
{
	private $id;
	private $firstname;
	private $middlename;
	private $lastname;
	private $email;
	private $phone;
	private $address;
	private $city;
	private $state;
	private $zip;
	private $street_address_id;
	private $subunit_id;
	private $neighborhoodAssociation;
	private $township;

	private $user_id;
	private $user;

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
				$result = $id;
			}
			else {
				$zend_db = Database::getConnection();
				if (is_numeric($id)) {
					$sql = 'select * from people where id=?';
				}
				elseif (false !== strpos($id,'@')) {
					$sql = 'select * from people where email=?';
				}
				else {
					$sql = 'select p.* from people p left join users on p.id=person_id where username=?';
				}
				$result = $zend_db->fetchRow($sql,array($id));
			}

			if ($result) {
				foreach ($result as $field=>$value) {
					if ($value) {
						$this->$field = $value;
					}
				}
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
		if (!$this->firstname) {
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
		$data['firstname'] = $this->firstname;
		$data['middlename'] = $this->middlename ? $this->middlename : null;
		$data['lastname'] = $this->lastname ? $this->lastname : null;
		$data['email'] = $this->email ? $this->email : null;
		$data['phone'] = $this->phone ? $this->phone : null;
		$data['address'] = $this->address ? $this->address : null;
		$data['city'] = $this->city ? $this->city : null;
		$data['state'] = $this->state ? $this->state : null;
		$data['zip'] = $this->zip ? $this->zip : null;
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
		$zend_db->update('people',$data,"id={$this->id}");
	}

	private function insert($data)
	{
		$zend_db = Database::getConnection();
		$zend_db->insert('people',$data);
		$this->id = $zend_db->lastInsertId('people','id');
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
	 * @return string
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}

	/**
	 * @return string
	 */
	public function getMiddlename()
	{
		return $this->middlename;
	}

	/**
	 * @return string
	 */
	public function getLastname()
	{
		return $this->lastname;
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * @return string
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * @return string
	 */
	public function getCity()
	{
		return $this->city;
	}

	/**
	 * @return string
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @return string
	 */
	public function getZip()
	{
		return $this->zip;
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
	 * @param string $string
	 */
	public function setFirstname($string)
	{
		$this->firstname = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setMiddlename($string)
	{
		$this->middlename = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setLastname($string)
	{
		$this->lastname = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setEmail($string)
	{
		$this->email = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setPhone($string)
	{
		$this->phone = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setAddress($string)
	{
		$this->address = trim($string);
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
	 * @param int $int
	 */
	public function setStreet_address_id($int)
	{
		$this->street_address_id = $int;
	}

	/**
	 * @param int $int
	 */
	public function setSubunit_id($int)
	{
		$this->subunit_id = $int;
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
	public function getFullname()
	{
		return "{$this->firstname} {$this->lastname}";
	}

	/**
	 * @return string
	 */
	public function getURL()
	{
		return BASE_URL.'/people/viewPerson.php?person_id='.$this->id;
	}

	/**
	 * @return int
	 */
	public function getUser_id()
	{
		if (!$this->user_id) {
			$zend_db = Database::getConnection();
			$this->user_id = $zend_db->fetchOne('select id from users where person_id=?',$this->id);
		}
		return $this->user_id;
	}

	/**
	 * @return User
	 */
	public function getUser()
	{
		if (!$this->user) {
			if ($this->getUser_id()) {
				$this->user = new User($this->getUser_id());
			}
		}
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		if ($this->getUser()) {
			return $this->getUser()->getUsername();
		}
	}

	/**
	 * @return Department
	 */
	public function getDepartment()
	{
		if ($this->getUser() && $this->getUser()->getDepartment_id()) {
			return $this->getUser()->getDepartment();
		}
	}

	/**
	 * @return TicketList
	 */
	public function getReportedTickets() {
		return new TicketList(array('reportedByPerson_id'=>$this->id));
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
