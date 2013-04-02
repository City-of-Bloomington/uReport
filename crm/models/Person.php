<?php
/**
 * @copyright 2009-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Person extends ActiveRecord
{
	protected $tablename = 'people';

	protected $department;

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
					$sql = "select p.* from people p
							left join peopleEmails e on p.id=e.person_id
							where email=?";
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
	}

	public function delete()
	{
		if ($this->getId()) {
			if ($this->hasTickets()) {
				throw new Exception('people/personStillHasTickets');
			}

			$zend_db = Database::getConnection();
			$zend_db->delete('peopleAddresses', 'person_id='.$this->getId());
			$zend_db->delete('peoplePhones',    'person_id='.$this->getId());
			$zend_db->delete('peopleEmails',    'person_id='.$this->getId());

			parent::delete();
		}
	}

	/**
	 * Removes all the user account related fields from this Person
	 */
	public function deleteUserAccount()
	{
		$userAccountFields = array(
			'username', 'password', 'authenticationMethod', 'role', 'department_id'
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
	public function getOrganization()  { return parent::get('organization'); }

	public function setFirstname   ($s) { parent::set('firstname',    $s); }
	public function setMiddlename  ($s) { parent::set('middlename',   $s); }
	public function setLastname    ($s) { parent::set('lastname',     $s); }
	public function setOrganization($s) { parent::set('organization', $s); }

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
	 * Updates fields that are not associated with authentication
	 *
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		$fields = array(
			'firstname', 'middlename', 'lastname', 'organization'
		);
		foreach ($fields as $field) {
			if (isset($post[$field])) {
				$set = 'set'.ucfirst($field);
				$this->$set($post[$field]);
			}
		}
	}

	/**
	 * Updates only the fields associated with authentication
	 *
	 * @param array $post
	 */
	public function handleUpdateUserAccount($post)
	{
		$this->handleUpdate($post);

		$fields = array('department_id','username','authenticationMethod','role');
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
	 * @return PhoneList
	 */
	public function getPhones()
	{
		if ($this->getId()) {
			return new PhoneList(array('person_id'=>$this->getId()));
		}
		return array();
	}

	/**
	 * @return EmailList
	 */
	public function getEmails()
	{
		if ($this->getId()) {
			return new EmailList(array('person_id'=>$this->getId()));
		}
		return array();
	}
	
	/**
	 * Temporary Fix for notifications
	 */
	public function getEmail()
	{
		$emails = $this->getEmails();
		if (count($emails)) {
			$e = $emails[0];
			return $e->getEmail();
		}
		return "";
	}

	/**
	 * @return EmailList
	 */
	public function getNotificationEmails()
	{
		return new EmailList(array('person_id'=>$this->getId(), 'usedForNotifications'=>1));
	}

	/**
	 * @return AddressList
	 */
	public function getAddresses()
	{
		if ($this->getId()) {
			return new AddressList(array('person_id'=>$this->getId()));
		}
		return array();
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
			if (!$subject) {
				$subject = APPLICATION_NAME.' Notification';
			}

			if ($personFrom) {
				$emails = $personFrom->getNotificationEmails();
				if (count($emails)) {
					$fromEmail    = $emails[0];
					$fromFullname = $personFrom->getFullname();
				}
			}
			if (!isset($fromEmail)) {
				$name = preg_replace('/[^a-zA-Z0-9]+/','_',APPLICATION_NAME);
				$fromEmail    = "$name@$_SERVER[SERVER_NAME]";
				$fromFullname = APPLICATION_NAME;

			}

			foreach ($this->getNotificationEmails() as $email) {
				$mail = new Zend_Mail();
				$mail->addTo($email->getEmail(),$this->getFullname());
				$mail->setFrom($fromEmail,$fromFullname);
				$mail->setSubject($subject);
				$mail->setBodyText($message);
				$mail->send();
			}
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
		$fieldname = trim($fieldname);
		$zend_db = Database::getConnection();

		$validFields = array('firstname', 'lastname', 'organization');
		if (in_array($fieldname, $validFields)) {
			$sql = "select distinct $fieldname from people where $fieldname like ?";
		}
		elseif ($fieldname == 'email') {
			$sql = "select distinct email from peopleEmails where email like ?";
		}
		return $zend_db->fetchCol($sql, array("$query%"));
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

		// We're going to be adding email and phone records for this person.
		// We have to save the person record before we can do the foreign keys.
		if (!$this->getId()) { $this->save(); }

		$list = $this->getEmails();
		if (!count($list) && $identity->getEmail()) {
			$email = new Email();
			$email->setPerson($this);
			$email->setEmail($identity->getEmail());
			$email->save();
		}
		$list = $this->getPhones();
		if (!count($list) && $identity->getPhone()) {
			$phone = new Phone();
			$phone->setPerson($this);
			$phone->setNumber($identity->getPhone());
			$phone->save();
		}
		$list = $this->getAddresses();
		if (!count($list) && $identity->getAddress()) {
			$address = new Address();
			$address->setPerson($this);
			$address->setAddress($identity->getAddress());
			$address->setCity   ($identity->getCity());
			$address->setState  ($identity->getState());
			$address->setZip    ($identity->setZip());
			$address->save();
		}
	}

	/**
	 * Transfers all data from a person, then deletes that person
	 *
	 * This person will end up containing all information from both people
	 * I took care to make sure to update the search index as well
	 * as the database.
	 *
	 * @param Person $person
	 */
	public function mergeFrom(Person $person)
	{
		if ($this->getId() && $person->getId()) {
			if($this->getId() == $person->getId()){
				// can not merge same person throw exception
				throw new Exception('mergerNotAllowed');
			}

			$zend_db = Database::getConnection();
			// Look up all the tickets we're about to modify
			// We need to remember them so we can update the search
			// index after we've updated the database
			$id = (int)$person->getId();
			$sql = "select distinct t.id from tickets t
					left join ticketHistory th on t.id=th.ticket_id
					left join issues         i on t.id= i.ticket_id
					left join issueHistory  ih on i.id=ih.issue_id
					left join media          m on i.id= m.issue_id
					left join responses      r on i.id= r.issue_id
					where ( t.enteredByPerson_id=$id or t.assignedPerson_id=$id or t.referredPerson_id=$id)
					   or (th.enteredByPerson_id=$id or th.actionPerson_id=$id)
					   or ( i.enteredByPerson_id=$id or i.reportedByPerson_id=$id)
					   or (ih.enteredByPerson_id=$id or ih.actionPerson_id=$id)
					   or m.person_id=$id or r.person_id=$id";
			$ticketIds = $zend_db->fetchCol($sql);

			$zend_db->beginTransaction();
			try {
				// These are all the database fields that hit the Solr index
				$zend_db->update('responses',    array(          'person_id'=> $this->getId()),          'person_id='.$person->getId());
				$zend_db->update('media',        array(          'person_id'=> $this->getId()),          'person_id='.$person->getId());
				$zend_db->update('issueHistory', array( 'enteredByPerson_id'=> $this->getId()), 'enteredByPerson_id='.$person->getId());
				$zend_db->update('issueHistory', array(    'actionPerson_id'=> $this->getId()),    'actionPerson_id='.$person->getId());
				$zend_db->update('issues',       array( 'enteredByPerson_id'=> $this->getId()), 'enteredByPerson_id='.$person->getId());
				$zend_db->update('issues',       array('reportedByPerson_id'=> $this->getId()),'reportedByPerson_id='.$person->getId());
				$zend_db->update('ticketHistory',array( 'enteredByPerson_id'=> $this->getId()), 'enteredByPerson_id='.$person->getId());
				$zend_db->update('ticketHistory',array(    'actionPerson_id'=> $this->getId()),    'actionPerson_id='.$person->getId());
				$zend_db->update('tickets',      array( 'enteredByPerson_id'=> $this->getId()), 'enteredByPerson_id='.$person->getId());
				$zend_db->update('tickets',      array(  'assignedPerson_id'=> $this->getId()),  'assignedPerson_id='.$person->getId());
				$zend_db->update('tickets',      array(  'referredPerson_id'=> $this->getId()),  'referredPerson_id='.$person->getId());

				// Fields that don't hit the Solr index
				$zend_db->update('clients',         array('contactPerson_id'=> $this->getId()), 'contactPerson_id='.$person->getId());
				$zend_db->update('departments',     array('defaultPerson_id'=> $this->getId()), 'defaultPerson_id='.$person->getId());
				$zend_db->update('peopleAddresses', array(       'person_id'=> $this->getId()),        'person_id='.$person->getId());
				$zend_db->update('peoplePhones',    array(       'person_id'=> $this->getId()),        'person_id='.$person->getId());
				$zend_db->update('peopleEmails',    array(       'person_id'=> $this->getId()),        'person_id='.$person->getId());

				$zend_db->delete('people','id='.$person->getId());
			}
			catch (Exception $e) {
				$zend_db->rollBack();
				throw($e);
			}
			$zend_db->commit();

			foreach ($ticketIds as $id) {
				$search = new Search();
				$ticket = new Ticket($id);
				$search->add($ticket);
			}
		}
	}
}
