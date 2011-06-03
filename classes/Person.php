<?php
/**
 * @copyright 2009-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Person extends MongoRecord
{
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
				// Mongo is case-sensitive
				// We need to clean and lowercase anything we're using
				// to do an exact match
				$id = strtolower(trim($id));
				if ($id) {
					$mongo = Database::getConnection();
					if (preg_match('/[0-9a-f]{24}/',$id)) {
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
			$this->setAuthenticationMethod('local');
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
		$mongo->people->save($this->data,array('safe'=>true));
	}

	public function delete()
	{
		if ($this->getId()) {
			if ($this->hasTickets()) {
				throw new Exception('people/personStillHasTickets');
			}

			$mongo = Database::getConnection();
			$mongo->people->remove(array('_id'=>$this->getId()));
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

	/**
	 * @return Department
	 */
	public function getDepartment()
	{
		if (isset($this->data['department'])) {
			return $this->data['department'];
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
		$this->data['email'] = strtolower(trim($string));
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

	/**
	 * @param string $string
	 */
	public function setDepartment($string)
	{
		$department = new Department($string);

		$this->data['department'] = array(
			'_id'=>$department->getId(),
			'name'=>$department->getName()
		);
	}
	//----------------------------------------------------------------
	// User Authentication implementation
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getUsername()
	{
		if (isset($this->data['username'])) {
			return $this->data['username'];
		}
	}

	/**
	 * @return string
	 */
	public function getAuthenticationMethod()
	{
		if (isset($this->data['authenticationMethod'])) {
			return $this->data['authenticationMethod'];
		}
	}

	/**
	 * @return array
	 */
	public function getRoles()
	{
		if (isset($this->data['roles'])) {
			return $this->data['roles'];
		}
		return array();
	}

	/**
	 * @return bool
	 */
	public function hasRole($role)
	{
		if (isset($this->data['roles'])) {
			return in_array($role,$this->data['roles']);
		}
	}

	/**
	 * @param string $string
	 */
	public function setUsername($string)
	{
		$this->data['username'] = strtolower(trim($string));
	}

	/**
	 * @param string $string
	 */
	public function setAuthenticationMethod($string)
	{
		$this->data['authenticationMethod'] = trim($string);
	}

	/**
	 * @param array $roles
	 */
	public function setRoles($roles)
	{
		$this->data['roles'] = $roles;
	}

	/**
	 * @param string $string
	 */
	public function setPassword($string)
	{
		$this->data['password'] = sha1($string);
	}

	/**
	 * Determines which authentication scheme to use for the user and calls the appropriate method
	 *
	 * Local users will get authenticated against the database
	 * Other authenticationMethods will need to write a class implementing ExternalAuthentication
	 * See: /libraries/framework/classes/ExternalAuthentication.php
	 *
	 * LDAP authentication is already provided
	 * /libraries/framework/classes/LDAP.php
	 *
	 * @param string $password
	 * @return boolean
	 */
	public function authenticate($password)
	{
		if ($this->getUsername())
		{
			switch ($this->getAuthenticationMethod()) {
				case 'local':
					return $this->getPassword()==sha1($password);
					break;
				default:
					return call_user_func(
						array($this->getAuthenticationMethod(),'authenticate'),
						$this->getUsername(),
						$password
					);
			}
		}
	}

	/**
	 * Checks if the user is supposed to have acces to the resource
	 *
	 * This is implemented by checking against a Zend_Acl object
	 * The Zend_Acl should be created in configuration.inc
	 *
	 * @param Zend_Acl_Resource|string $resource
	 * @return boolean
	 */
	public function IsAllowed($resource)
	{
		global $ZEND_ACL;
		if ($this->getRoles()) {
			foreach ($this->getRoles() as $role) {
				if ($ZEND_ACL->isAllowed($role,$resource)) {
					return true;
				}
			}
		}
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
	public function getDepartment_id()
	{
		if (isset($this->data['department']['_id'])) {
			return $this->data['department']['_id'];
		}
	}

	/**
	 * @return string
	 */
	public function getURL()
	{
		if ($this->getId()) {
			return BASE_URL."/people/viewPerson.php?person_id={$this->getId()}";
		}
	}

	/**
	 * @param string $personField The field in Ticket that has this person embedded
	 * @param array $fields Additional fields to filter the ticketList
	 * @return TicketList
	 */
	public function getTickets($personFieldname,$fields=null)
	{
		if ($this->getId()) {
			$field = $personFieldname.'Person._id';
			if (is_array($fields)) {
				$search = $fields;
				$search[$field] = (string)$this->getId();
			}
			else {
				$search = array($field=>(string)$this->getId());
			}
			return new TicketList($search);
		}
	}

	/**
	 * Returns true if this person's ID is associated with any fields in the ticket records
	 *
	 * @return boolean
	 */
	public function hasTickets()
	{
		if ($this->getId()) {
			$mongo = Database::getConnection();
			$tickets = $mongo->tickets->findOne(array(
				'$or'=>array(
					array('enteredByPerson._id'=>new MongoId($this->data['_id'])),
					array('assignedPerson._id'=>new MongoId($this->data['_id'])),
					array('referredPerson._id'=>new MongoId($this->data['_id'])),
					array('issues.enteredByPerson._id'=>new MongoId($this->data['_id'])),
					array('issues.reportedByPerson._id'=>new MongoId($this->data['_id'])),
					array('issues.responses.person._id'=>new MongoId($this->data['_id'])),
					array('issues.media.person._id'=>new MongoId($this->data['_id'])),
					array('history.enteredByPerson._id'=>new MongoId($this->data['_id'])),
					array('history.actionPerson._id'=>new MongoId($this->data['_id']))
				)
			));
			if ($tickets) {
				return true;
			}
		}
	}

	/**
	 * Returns the array of distinct values used for Tickets in the system
	 *
	 * @param string $fieldname
	 * @param string $query Text to match in the $fieldname
	 * @return array
	 */
	public static function getDistinct($fieldname,$query=null)
	{
		$fieldname = trim($fieldname);

		$mongo = Database::getConnection();
		$command = array('distinct'=>'people','key'=>$fieldname);
		if ($query) {
			$query = trim($query);
			$regex = new MongoRegex("/$query/i");
			$command['query'] = array($fieldname=>$regex);
		}
		$result = $mongo->command($command);
		return $result['values'];
	}
}
