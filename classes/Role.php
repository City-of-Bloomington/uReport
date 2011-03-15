<?php
/**
 * @copyright 2006-2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Role
{
	private $id;
	private $name;

	/**
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in an int will load the data from the database for the given ID.
	 *
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 *
	 * @param int|string|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$result = $id;
			}
			else {
				$sql = is_numeric($id)
					? 'select * from roles where id=?'
					: 'select * from roles where name=?';

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
				throw new Exception('roles/unknownRole');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	/**
	 * Throws an exception if theres anything wrong
	 * @throws Exception
	 */
	public function validate()
	{
		if (!$this->name) {
			throw new Exception('missingName');
		}
	}

	/**
	 * This generates generic SQL that should work right away.
	 * You can replace this $fields code with your own custom SQL
	 * for each property of this class,
	 */
	public function save()
	{
		$this->validate();

		$data = array();
		$data['name'] = $this->name;


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
		$zend_db->update('roles',$data,"id={$this->id}");
	}

	private function insert($data)
	{
		$zend_db = Database::getConnection();
		$zend_db->insert('roles',$data);
		$this->id = $zend_db->lastInsertId('roles','id');
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
	public function getName()
	{
		return $this->name;
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------
	/**
	 * @param string $string
	 */
	public function setName($string)
	{
		$this->name = trim($string);
	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->name;
	}
}
