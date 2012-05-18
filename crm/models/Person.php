<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Person extends ActiveRecord
{
	protected $tablename = 'people';
	protected $allowsDelete = true;

	private $department;
	private $phones = array();

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
				if (ctype_digit($id)) {
					$sql = 'select * from people where id=?';
				}
				elseif (false !== strpos($id,'@')) {
					$sql = 'select * from people where email=?';
				}
				else {
					$sql = 'select * from people where username=?';
				}
				$result = $zend_db->fetchRow($sql, array($id));
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
		if ((!$this->data['firstname'] && !$this->data['lastname'])
			&& !$this->data['organization']) {
			throw new Exception('missingRequiredFields');
		}

		if (isset($this->data['username']) && !isset($this->data['authenticationMethod'])) {
			$this->data['authenticationMethod'] = 'local';
		}
	}

	public function delete()
	{
		if ($this->getId()) {
			if ($this->hasTickets()) {
				throw new Exception('people/personStillHasTickets');
			}
			parent::delete();
		}
	}

	/**
	 * Removes all the user account related fields from this Person
	 */
	public function deleteUserAccount()
	{
		$userAccountFields = array(
			'username', 'password', 'authenticationMethod', 'roles', 'department_id'
		);
		foreach ($userAccountFields as $f) {
			$this->data[$f] = null;
		}
		$this->department = null;
	}


	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()            { return parent::get('id');           }
	public function getFirstname()     { return parent::get('firstname');    }
	public function getMiddlename()    { return parent::get('middlename');   }
	public function getLastname()      { return parent::get('lastname');     }
	public function getEmail()         { return parent::get('email');        }
	public function getOrganization()  { return parent::get('organization'); }
	public function getAddress()       { return parent::get('address');      }
	public function getCity()          { return parent::get('city');         }
	public function getState()         { return parent::get('state');        }
	public function getZip()           { return parent::get('zip');          }

	public function setFirstname   ($s) { $this->data['firstname']    = trim($s); }
	public function setMiddlename  ($s) { $this->data['middlename']   = trim($s); }
	public function setLastname    ($s) { $this->data['lastname']     = trim($s); }
	public function setEmail       ($s) { $this->data['email']        = trim($s); }
	public function setOrganization($s) { $this->data['organization'] = trim($s); }
	public function setAddress     ($s) { $this->data['address']      = trim($s); }
	public function setCity        ($s) { $this->data['city']         = trim($s); }
	public function setState       ($s) { $this->data['state']        = trim($s); }
	public function setZip         ($s) { $this->data['zip']          = trim($s); }

	public function getDepartment_id()    { return parent::get('department_id'); }
	public function getDepartment()       { return parent::getForeignKeyObject('Department', 'department_id');      }
	public function setDepartment_id($id)        { parent::setForeignKeyField ('Department', 'department_id', $id); }
	public function setDepartment(Department $d) { parent::setForeignKeyObject('Department', 'department_id', $d);  }

	public function getUsername()             { return parent::get('username'); }
	public function getPassword()             { return parent::get('password'); } # Encrypted
	public function getRole()                 { return parent::get('role');     }
	public function getAuthenticationMethod() { return parent::get('authenticationMethod'); }

	public function setUsername            ($s) { $this->data['username']             = trim($s); }
	public function setPassword            ($s) { $this->data['password']             = sha1($s); }
	public function setRole                ($s) { $this->data['role']                 = trim($s); }
	public function setAuthenticationMethod($s) { $this->data['authenticationMethod'] = trim($s); }

	/**
	 * @param array $post
	 */
	public function set($post)
	{
		$fields = array('firstname','lastname','email','department',
						'username','authenticationMethod','role');
		foreach ($fields as $f) {
			if (isset($post[$f])) {
				$set = 'set'.ucfirst($f);
				$this->$set($post[$f]);
			}
			if (!empty($post['password'])) {
				$this->setPassword($post['password']);
			}
		}

		$method = $this->getAuthenticationMethod();
		if ($this->getUsername() && $method && $method != 'local') {
			$identity = new $method($this->getUsername());
			$this->populateFromExternalIdentity($identity);
		}
	}

	//----------------------------------------------------------------
	// User Authentication
	//----------------------------------------------------------------
	/**
	 * Should provide the list of methods supported
	 *
	 * There should always be at least one method, called "local"
	 * Additional methods must match classes that implement External Identities
	 * See: ExternalIdentity.php
	 *
	 * @return array
	 */
	public static function getAuthenticationMethods()
	{
		global $DIRECTORY_CONFIG;
		return array_merge(array('local'), array_keys($DIRECTORY_CONFIG));
	}

	/**
	 * Determines which authentication scheme to use for the user and calls the appropriate method
	 *
	 * Local users will get authenticated against the database
	 * Other authenticationMethods will need to write a class implementing ExternalIdentity
	 * See: /libraries/framework/classes/ExternalIdentity.php
	 *
	 * @param string $password
	 * @return boolean
	 */
	public function authenticate($password)
	{
		if ($this->getUsername()) {
			switch($this->getAuthenticationMethod()) {
				case "local":
					return $this->getPassword()==sha1($password);
				break;

				default:
					$method = $this->getAuthenticationMethod();
					return $method::authenticate($this->getUsername(),$password);
			}
		}
	}

	/**
	 * Checks if the user is supposed to have acces to the resource
	 *
	 * This is implemented by checking against a Zend_Acl object
	 * The Zend_Acl should be created in configuration.inc
	 *
	 * @param string $resource
	 * @param string $action
	 * @return boolean
	 */
	public function IsAllowed($resource, $action=null)
	{
		global $ZEND_ACL;
		$role = $this->getRole() ? $this->getRole() : 'Anonymous';
		return $ZEND_ACL->isAllowed($role, $resource, $action);
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Returns an array of Phones indexed by Id
	 *
	 * @return array
	 */
	public function getPhones()
	{
		if (!count($this->phones && $this->getId()) {
			$phones = new PhoneList(array('person_id'=>$this->getId()));
			foreach ($phones as $p) {
				$this->phones[$p->getId()] = $p;
			}
		}
		return $this->phones;
	}

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
			return BASE_URL."/people/view?person_id={$this->getId()}";
		}
	}

	/**
	 * @param string $personField The field in Ticket that has this person embedded
	 * @param array $fields Additional fields to filter the ticketList
	 * @return TicketList
	 */
	public function getTickets($personFieldname, $fields=null)
	{
		if ($this->getId()) {
			$field = $personFieldname.'Person_id';
			if (is_array($fields)) {
				$search = $fields;
				$search[$field] = $this->getId();
			}
			else {
				$search = array($field=>$this->getId());
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
	 * @param string $message
	 * @param string $subject
	 * @param Person $personFrom
	 */
	public function sendNotification($message, $subject=null, Person $personFrom=null)
	{
		if (defined('NOTIFICATIONS_ENABLED') && NOTIFICATIONS_ENABLED) {
			if (!$personFrom) {
				$personFrom = new Person();
				$name = preg_replace('/[^a-zA-Z0-9]+/','_',APPLICATION_NAME);
				$personFrom->setEmail("$name@$_SERVER[SERVER_NAME]");
			}
			if (!$subject) {
				$subject = APPLICATION_NAME.' Notification';
			}
			$mail = new Zend_Mail();
			$mail->addTo($this->getEmail(),$this->getFullname());
			$mail->setFrom($personFrom->getEmail(),$personFrom->getFullname());
			$mail->setSubject($subject);
			$mail->setBodyText($message);
			$mail->send();
		}
	}

	/**
	 * @param ExternalIdentity $identity An object implementing ExternalIdentity
	 */
	public function populateFromExternalIdentity(ExternalIdentity $identity)
	{
		if (!$this->getFirstname() && $identity->getFirstname()) {
			$this->setFirstname($identity->getFirstname());
		}
		if (!$this->getLastname() && $identity->getLastname()) {
			$this->setLastname($identity->getLastname());
		}
		if (!$this->getEmail() && $identity->getEmail()) {
			$this->setEmail($identity->getEmail());
		}
		if (!$this->getPhoneNumber() && $identity->getPhone()) {
			$this->setPhoneNumber($identity->getPhone());
		}
		if (!$this->getAddress() && $identity->getAddress()) {
			$this->setAddress($identity->getAddress());
		}
		if (!$this->getCity() && $identity->getCity()) {
			$this->setCity($identity->getCity());
		}
		if (!$this->getState() && $identity->getState()) {
			$this->setState($identity->getState());
		}
		if (!$this->getZip() && $identity->getZip()) {
			$this->setZip($identity->getZip());
		}
	}
}
