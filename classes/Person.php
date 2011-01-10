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
	private $lastname;
	private $email;

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
				if (ctype_digit($id)) {
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
		if (!$this->firstname || !$this->lastname) {
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
		$data['lastname'] = $this->lastname;
		$data['email'] = $this->email ? $this->email : null;

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
	public function getUser() {
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
	public function getUsername() {
		if ($this->getUser()) {
			return $this->getUser()->getUsername();
		}
	}
}
