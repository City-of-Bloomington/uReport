<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class User extends SystemUser
{
	private $id;
	private $person_id;
	private $username;
	private $password;
	private $authenticationMethod;

	private $person;
	private $roles = array();
	private $newPassword; // the User's new password, unencrypted

	/**
	 * @param int|string $id
	 */
	public function __construct($id = null)
	{
		if ($id) {
			if (is_array($id)) {
				$result = $id;
			}
			else {
				if (ctype_digit($id)) {
					$sql = 'select * from users where id=?';
				}
				else {
					$sql = 'select * from users where username=?';
				}
				$zend_db = Database::getConnection();
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
				throw new Exception('users/unknownUser');
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
		if (!$this->person_id) {
			throw new Exception('users/missingPerson_id');
		}
		if (!$this->username) {
			throw new Exception('users/missingUsername');
		}

	}

	/**
	 * Saves this record back to the database
	 *
	 * This generates generic SQL that should work right away.
	 * You can replace this $fields code with your own custom SQL
	 * for each property of this class,
	 */
	public function save()
	{
		$this->validate();

		$data = array();
		$data['person_id'] = $this->person_id;
		$data['username'] = $this->username;
		// Passwords should not be updated by default.  Use the savePassword() function
		$data['authenticationMethod'] = $this->authenticationMethod
										? $this->authenticationMethod
										: null;

		// Do the database calls
		if ($this->id) {
			$this->update($data);
		}
		else {
			$this->insert($data);
		}

		// Save the password only if it's changed
		if ($this->passwordHasChanged()) {
			$this->savePassword();
		}

		$this->updateRoles();
	}

	private function update($data)
	{
		$zend_db = Database::getConnection();
		$zend_db->update('users',$data,"id={$this->id}");
	}

	private function insert($data)
	{
		$zend_db = Database::getConnection();
		$zend_db->insert('users',$data);
		$this->id = $zend_db->lastInsertId('users','id');
	}

	/**
	 * Removes this object from the database
	 */
	public function delete()
	{
		$zend_db = Database::getConnection();
		$zend_db->delete('user_roles',"user_id={$this->id}");
		$zend_db->delete('users',"id={$this->id}");
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
	 * @return int
	 */
	public function getPerson_id()
	{
		return $this->person_id;
	}
	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}
	/**
	 * @return string
	 */
	public function getAuthenticationMethod()
	{
		return $this->authenticationMethod;
	}
	/**
	 * @return Person
	 */
	public function getPerson()
	{
		if ($this->person_id) {
			if (!$this->person) {
				$this->person = new Person($this->person_id);
			}
			return $this->person;
		}
		return null;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------
	/**
	 * @param int $int
	 */
	public function setPerson_id($int)
	{
		$this->person = new Person($int);
		$this->person_id = $int;
	}
	/**
	 * @param string $string
	 */
	public function setUsername($string)
	{
		$this->username = trim($string);
	}
	/**
	 * Takes a user-given password and converts it to an MD5 Hash
	 * @param String $string
	 */
	public function setPassword($string)
	{
		// Save the user given password, so we can update it externally, if needed
		$this->newPassword = trim($string);
		$this->password = md5(trim($string));
	}
	/**
	 * Takes a pre-existing MD5 hash
	 * @param MD5 $hash
	 */
	public function setPasswordHash($hash)
	{
		$this->password = trim($hash);
	}
	/**
	 * @param string $authenticationMethod
	 */
	public function setAuthenticationMethod($string)
	{
		$this->authenticationMethod = $string;
		if ($this->authenticationMethod != 'local') {
			$this->password = null;
			$this->saveLocalPassword();
		}
	}
	/**
	 * @param Person $person
	 */
	public function setPerson($person)
	{
		$this->person_id = $person->getId();
		$this->person = $person;
	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getFirstname()
	{
		return $this->getPerson()->getFirstname();
	}
	/**
	 * @return string
	 */
	public function getLastname()
	{
		return $this->getPerson()->getLastname();
	}
	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->getPerson()->getEmail();
	}
	/**
	 * Returns an array of Role names with the role id as the array index
	 *
	 * @return array
	 */
	public function getRoles()
	{
		if (!count($this->roles)) {
			if ($this->id) {
				$zend_db = Database::getConnection();
				$select = new Zend_Db_Select($zend_db);
				$select->from('user_roles','role_id')
						->joinLeft('roles','role_id=id','name')
						->where('user_id=?');
				$result = $zend_db->fetchAll($select,$this->id);

				foreach ($result as $row) {
					$this->roles[$row['role_id']] = $row['name'];
				}
			}
		}
		return $this->roles;
	}
	/**
	 * Takes an array of role names.  Loads the Roles from the database
	 *
	 * @param array $roleNames An array of names
	 */
	public function setRoles($roleNames)
	{
		$this->roles = array();
		foreach ($roleNames as $name) {
			$role = new Role($name);
			$this->roles[$role->getId()] = $role->getName();
		}
	}
	/**
	 * Takes a string or an array of strings and checks if the user has that role
	 *
	 * @param Array|String $roles
	 * @return boolean
	 */
	public function hasRole($roles)
	{
		if (is_array($roles)) {
			foreach ($roles as $roleName) {
				if (in_array($roleName,$this->getRoles())) {
					return true;
				}
			}
			return false;
		}
		else {
			return in_array($roles,$this->getRoles());
		}
	}

	/**
	 * Saves the current roles back to the database
	 */
	private function updateRoles()
	{
		$zend_db = Database::getConnection();

		$roles = $this->getRoles();

		$zend_db->delete('user_roles',"user_id={$this->id}");

		foreach ($roles as $id=>$name) {
			$data = array('user_id'=>$this->id,'role_id'=>$id);
			$zend_db->insert('user_roles',$data);
		}
	}

	/**
	 * Since passwords can be stored externally, we only want to bother trying
	 * to save them when they've actually changed
	 * @return boolean
	 */
	public function passwordHasChanged()
	{
		return $this->newPassword ? true : false;
	}

	/**
	 * Callback function from the SystemUser class
	 * The SystemUser will determine where the password should be stored.
	 * If the password is stored locally, it will call this function
	 */
	protected function saveLocalPassword()
	{
		if ($this->id) {
			$zend_db = Database::getConnection();

			// Passwords in the class should already be MD5 hashed
			$zend_db->update('users',array('password'=>$this->password),"id={$this->id}");
		}
	}

	/**
	 * Callback function from the SystemUser class
	 *
	 * The SystemUser class will determine where the authentication
	 * should occur.  If the user should be authenticated locally,
	 * this function will be called.
	 *
	 * @param string $password
	 * @return boolean
	 */
	protected function authenticateDatabase($password)
	{
		$zend_db = Database::getConnection();

		$md5 = md5($password);

		$id = $zend_db->fetchOne('select id from users where username=? and password=?',
								array($this->username,$md5));
		return $id ? true : false;
	}
}
