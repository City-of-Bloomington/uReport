<?php
/**
 * @copyright 2009-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\ActiveRecord;
use Application\Database;
use Application\Models\Email;

use Blossom\Classes\ExternalIdentity;
use PHPMailer\PHPMailer\PHPMailer;

class Person extends ActiveRecord
{
	protected $tablename = 'people';

	protected $department;

	const ERROR_UNKNOWN_PERSON = 'people/unknown';

	/**
	 * Returns the matching Person object or null if not found
	 *
	 * @return Person
	 */
	public static function findByUsername(string $username)
	{
        $db = Database::getConnection();
        $sql = 'select * from people where username=?';

        $result = $db->createStatement($sql)->execute([$username]);
        if (count($result)) {
            return new Person($result->current());
        }
	}

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
				$this->exchangeArray($id);
			}
			else {
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

				$db = Database::getConnection();
				$result = $db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception(self::ERROR_UNKNOWN_PERSON);
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setAuthenticationMethod('local');
		}
	}

    /**
     * When repopulating with fresh data, make sure to set default
     * values on all object properties.
     *
     * @Override
     * @param array $data
     */
    public function exchangeArray($data)
    {
        parent::exchangeArray($data);

        $this->department = null;
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
			throw new \Exception('missingRequiredFields');
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
				throw new \Exception('people/personStillHasTickets');
			}

			$db = Database::getConnection();
			$db->query('delete from peopleAddresses where person_id=?', [$this->getId()]);
			$db->query('delete from peoplePhones where person_id=?',    [$this->getId()]);
			$db->query('delete from peopleEmails where person_id=?',    [$this->getId()]);

			parent::delete();
		}
	}

	/**
	 * Removes all the user account related fields from this Person
	 */
	public function deleteUserAccount()
	{
		$userAccountFields = [
			'username', 'password', 'authenticationMethod', 'role', 'department_id'
		];
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
	public function getDepartment()       { return parent::getForeignKeyObject(__namespace__.'\Department', 'department_id');      }
	public function setDepartment_id($id)        { parent::setForeignKeyField (__namespace__.'\Department', 'department_id', $id); }
	public function setDepartment(Department $d) { parent::setForeignKeyObject(__namespace__.'\Department', 'department_id', $d);  }

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
            global $DIRECTORY_CONFIG;

            $class = $DIRECTORY_CONFIG[$method]['classname'];
			$identity = new $class($this->getUsername());
			$this->populateFromExternalIdentity($identity);
		}
	}

	/**
	 * @param array $post
	 */
	public function handleChangePassword($post)
	{
		if (   !empty($post['current_password'])
			&& !empty($post['new_password'])
			&& !empty($post['retype_password'])) {

			if ($this->authenticate($_POST['current_password'])) {
				if ($post['new_password'] == $post['retype_password']) {
					$this->setPassword($post['new_password']);
				}
				else {
					throw new \Exception('users/passwordsDontMatch');
				}
			}
			else {
				throw new \Exception('users/wrongPassword');
			}
		}
		else {
			throw new \Exception('missingRequiredFields');
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
        global $DIRECTORY_CONFIG;

		if ($this->getUsername()) {
			switch($this->getAuthenticationMethod()) {
				case "local":
					return $this->getPassword()==sha1($password);
				break;

				default:
					$method = $this->getAuthenticationMethod();
					$class = $DIRECTORY_CONFIG[$method]['classname'];
					return $class::authenticate($this->getUsername(),$password);
			}
		}
	}

	/**
	 * Checks if the user is supposed to have acces to the resource
	 *
	 * This is implemented by checking against a Laminas Acl object
	 * The Laminas Acl should be created in bootstrap.inc
	 *
	 * @param string $resource
	 * @param string $action
	 * @return boolean
	 */
	public static function isAllowed($resource, $action=null)
	{
		global $ACL;
		$role = 'Anonymous';
		if (isset($_SESSION['USER']) && $_SESSION['USER']->getRole()) {
			$role = $_SESSION['USER']->getRole();
		}
		return $ACL->isAllowed($role, $resource, $action);
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
            $table = new PhoneTable();
			return $table->find( ['person_id'=>$this->getId()] );
		}
		return array();
	}

	/**
	 * @return EmailList
	 */
	public function getEmails()
	{
		if ($this->getId()) {
            $table = new EmailTable();
			return $table->find( ['person_id'=>$this->getId()] );
		}
		return array();
	}

	/**
	 * @return EmailList
	 */
	public function getNotificationEmails()
	{
        $table = new EmailTable();
		return $table->find( ['person_id'=>$this->getId(), 'usedForNotifications'=>1] );
	}

	/**
	 * @return AddressList
	 */
	public function getAddresses()
	{
		if ($this->getId()) {
            $table = new AddressTable();
			return $table->find( ['person_id'=>$this->getId()] );
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
				$search = [$field=>$this->getId()];
			}
			$table = new TicketTable();
			return $table->find($search);
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
			$db = Database::getConnection();
			// This query is written as a Union for speed
			// A Union is the only way to use the indexes for this query
			$sql = "(select t.id from tickets t
					where t.enteredByPerson_id =$id
					   or t.assignedPerson_id  =$id
					   or t.reportedByPerson_id=$id
					limit 1)
					union all
					(select h.ticket_id from ticketHistory h
					where h.enteredByPerson_id=$id
					   or h.actionPerson_id=$id
					limit 1)
					union all
					(select m.ticket_id from media m
					where m.person_id=$id
					limit 1)";
			$result = $db->createStatement($sql)->execute();
			return $result->count() ? true : false;
		}
	}

    /**
     * @param string $message
     * @param string $subject
     * @param string $replyTo
     */
    public function sendNotification($message, $subject=null, $replyTo=null)
    {
        if (defined('NOTIFICATIONS_ENABLED') && NOTIFICATIONS_ENABLED) {
            if (!$subject) {
                $subject = APPLICATION_NAME.' Notification';
            }

            $mail = new PHPMailer(true);
            $mail->isHTML(false);
            $mail->isSMTP();
            $mail->Host        = SMTP_HOST;
            $mail->Port        = SMTP_PORT;
            $mail->SMTPSecure  = false;
            $mail->SMTPAutoTLS = false;
            $mail->Subject     = $subject;
            $mail->Body        = $message;
            $mail->setFrom('no-reply@'.BASE_HOST, APPLICATION_NAME);

            foreach ($this->getNotificationEmails() as $email) {
                if (Email::isValidEmail($email->getEmail())) {
                    $mail->addAddress($email->getEmail());
                    $mail->send();
                }
                $mail->clearAddresses();
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
		$db = Database::getConnection();

		$validFields = array('firstname', 'lastname', 'organization');
		if (in_array($fieldname, $validFields)) {
			$sql = "select distinct $fieldname from people where $fieldname like ?";
		}
		elseif ($fieldname == 'email') {
			$sql = "select distinct email from peopleEmails where email like ?";
		}
		$result = $db->createStatement($sql)->execute(["$query%"]);
		$o = [];
		foreach ($result as $row) { $o[] = $row[$fieldname]; }
		return $o;
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
				throw new \Exception('people/mergerNotAllowed');
			}

			$db = Database::getConnection();
			// Look up all the tickets we're about to modify
			// We need to remember them so we can update the search
			// index after we've updated the database
			$id = (int)$person->getId();
			$sql = "select distinct t.id from tickets t
					left join ticketHistory th on t.id=th.ticket_id
					left join media          m on t.id= m.ticket_id
					where ( t.enteredByPerson_id=$id or t.assignedPerson_id=$id or t.reportedByPerson_id=$id)
					   or (th.enteredByPerson_id=$id or  th.actionPerson_id=$id)
					   or m.person_id=$id";
			$result = $db->query($sql)->execute();
			$ticketIds = [];
			foreach ($result as $row) {
				$ticketIds[] = $row['id'];
			}

			$db->getDriver()->getConnection()->beginTransaction();
			try {
				// These are all the database fields that hit the Solr index
				$db->query('update media         set           person_id=? where           person_id=?')->execute([$this->getId(), $person->getId()]);
				$db->query('update tickets       set reportedByPerson_id=? where reportedByPerson_id=?')->execute([$this->getId(), $person->getId()]);
				$db->query('update ticketHistory set  enteredByPerson_id=? where  enteredByPerson_id=?')->execute([$this->getId(), $person->getId()]);
				$db->query('update ticketHistory set     actionPerson_id=? where     actionPerson_id=?')->execute([$this->getId(), $person->getId()]);
				$db->query('update tickets       set  enteredByPerson_id=? where  enteredByPerson_id=?')->execute([$this->getId(), $person->getId()]);
				$db->query('update tickets       set   assignedPerson_id=? where   assignedPerson_id=?')->execute([$this->getId(), $person->getId()]);

				// Fields that don't hit the Solr index
				$db->query('update clients         set contactPerson_id=? where contactPerson_id=?')->execute([$this->getId(), $person->getId()]);
				$db->query('update departments     set defaultPerson_id=? where defaultPerson_id=?')->execute([$this->getId(), $person->getId()]);
				$db->query('update peopleAddresses set        person_id=? where        person_id=?')->execute([$this->getId(), $person->getId()]);
				$db->query('update peoplePhones    set        person_id=? where        person_id=?')->execute([$this->getId(), $person->getId()]);
				$db->query('update peopleEmails    set        person_id=? where        person_id=?')->execute([$this->getId(), $person->getId()]);

				$db->query('delete from people where id=?')->execute([$person->getId()]);
			}
			catch (Exception $e) {
				$db->getDriver()->getConnection()->rollback();
				throw($e);
			}
			$db->getDriver()->getConnection()->commit();

			foreach ($ticketIds as $id) {
				$search = new Search();
				$ticket = new Ticket($id);
				$search->add($ticket);
			}
		}
	}
}
