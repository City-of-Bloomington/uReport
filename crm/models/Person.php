<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Person extends ActiveRecord
{
	protected $tablename = 'people';

	protected $department;
	protected $phones = array();
	protected $phonesUpdated = false;

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
				if (ActiveRecord::isId($id)) {
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
		if ((!$this->getFirstname() && !$this->getLastname())
			&& !$this->getOrganization()) {
			throw new Exception('missingRequiredFields');
		}

		if ($this->getUsername() && !$this->getAuthenticationMethod()) {
			$this->setAuthenticationMethod('local');
		}
	}

	public function save()
	{
		parent::save();
		if ($this->phonesUpdated) {
			foreach ($this->phones as $phone) {
				if (!$phone->getPerson_id()) {
					$phone->setPerson($this);
				}
				$phone->save();
			}
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

	public function setFirstname   ($s) { parent::set('firstname',    $s); }
	public function setMiddlename  ($s) { parent::set('middlename',   $s); }
	public function setLastname    ($s) { parent::set('lastname',     $s); }
	public function setEmail       ($s) { parent::set('email',        $s); }
	public function setOrganization($s) { parent::set('organization', $s); }
	public function setAddress     ($s) { parent::set('address',      $s); }
	public function setCity        ($s) { parent::set('city',         $s); }
	public function setState       ($s) { parent::set('state',        $s); }
	public function setZip         ($s) { parent::set('zip',          $s); }

	public function getDepartment_id()    { return parent::get('department_id'); }
	public function getDepartment()       { return parent::getForeignKeyObject('Department', 'department_id');      }
	public function setDepartment_id($id)        { parent::setForeignKeyField ('Department', 'department_id', $id); }
	public function setDepartment(Department $d) { parent::setForeignKeyObject('Department', 'department_id', $d);  }

	public function getUsername()             { return parent::get('username'); }
	public function getPassword()             { return parent::get('password'); } # Encrypted
	public function getRole()                 { return parent::get('role');     }
	public function getAuthenticationMethod() { return parent::get('authenticationMethod'); }

	public function setUsername            ($s) { parent::set('username',             $s); }
	public function setRole                ($s) { parent::set('role',                 $s); }
	public function setAuthenticationMethod($s) { parent::set('authenticationMethod', $s); }

	public function setPassword($s)
	{
		$s = trim($s);
		if ($s) { $this->data['password'] = sha1($s); }
		else    { $this->data['password'] = null;     }
	}

	/**
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		$fields = array(
			'firstname', 'middlename', 'lastname', 'email', 'organization',
			'phoneNumber', 'phoneDeviceId',
			'address', 'city', 'state', 'zip'
		);
		foreach ($fields as $field) {
			if (isset($post[$field])) {
				$set = 'set'.ucfirst($field);
				$this->$set($post[$field]);
			}
		}
	}

	/**
	 * @param array $post
	 */
	public function handleUpdateUserAccount($post)
	{
		$fields = array('firstname','lastname','email','department_id',
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
		if (!count($this->phones) && $this->getId()) {
			$this->phones = new PhoneList(array('person_id'=>$this->getId()));
		}
		return $this->phones;
	}

	/**
	 * Makes sure there is at least one phone loaded
	 *
	 * All the forms on the system currently expect only one phone
	 * We are in a transition to storing many phones per person.
	 * There are several instances where we treat the person as if
	 * they only have one phone, though.
	 * For instance, Open311 only supports one phone per person
	 * For these getters/setters we are treating the first phone record
	 * as their only phone.
	 */
	private function loadFirstPhoneForEditing()
	{
		$this->phonesUpdated = true;
		$this->getPhones();

		if (!isset($this->phones[0])) {
			$this->phones[0] = new Phone();
			$this->phones[0]->setPerson($this);
		}
	}
	public function getPhoneNumber()
	{
		$phones = $this->getPhones();
		if (count($phones)) { return $phones[0]->getNumber(); }
	}
	public function getPhoneDeviceId()
	{
		$phones = $this->getPhones();
		if (count($phones)) { return $phones[0]->getDeviceId(); }
	}
	public function setPhoneNumber($number)
	{
		$this->loadFirstPhoneForEditing();
		$this->phones[0]->setNumber($number);
	}
	public function setPhoneDeviceId($id)
	{
		$this->loadFirstPhoneForEditing();
		$this->phones[0]->setDeviceId($id);
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
		$id = (int)$this->getId();
		if ($id) {
			$zend_db = Database::getConnection();
			$fields = array(
				"t.enteredByPerson_id=$id",
				"t.assignedPerson_id=$id",
				"t.referredPerson_id=$id",
				"h.enteredByPerson_id=$id",
				"h.actionPerson_id=$id",
				"i.enteredByPerson_id=$id",
				"i.reportedByPerson_id=$id",
				"r.person_id=$id",
				"m.person_id=$id"
			);
			$or = implode(' or ', $fields);
			$sql = "select t.id from tickets t
					left join ticketHistory h on t.id=h.ticket_id
					left join issues i on t.id=i.ticket_id
					left join responses r on i.id=r.issue_id
					left join media m on i.id=m.issue_id
					where ($or) limit 1";
			$result = $zend_db->fetchCol($sql);
			return count($result) ? true : false;
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
	 * Returns the array of distinct field values for People records
	 *
	 * This is primarily used to populate autocomplete lists for search forms
	 * Make sure to keep this function as fast as possible
	 *
	 * @param string $fieldname
	 * @param string $query Text to match in the $fieldname
	 * @return array
	 */
	public static function getDistinct($fieldname, $query=null)
	{
		$validFields = array('firstname', 'lastname', 'email', 'organization');
		$fieldname = trim($fieldname);
		if (in_array($fieldname, $validFields)) {
			$zend_db = Database::getConnection();
			$sql = "select distinct $fieldname from people where $fieldname like ?";
			return $zend_db->fetchCol($sql, array("$query%"));
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
