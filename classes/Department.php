<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Department
{
	private $id;
	private $name;
	private $default_person_id;

	private $actions = array();
	private $categories = array();
	private $default_person;

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
	 * @param int|array $id
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$result = $id;
			}
			else {
				$zend_db = Database::getConnection();
				$sql = 'select * from departments where id=?';
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
				throw new Exception('departments/unknownDepartment');
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
		if (!$this->name || !$this->default_person_id) {
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
		$data['name'] = $this->name;
		$data['default_person_id'] = $this->default_person_id;

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
		$zend_db->update('departments',$data,"id='{$this->id}'");
	}

	private function insert($data)
	{
		$zend_db = Database::getConnection();
		$zend_db->insert('departments',$data);
		$this->id = $zend_db->lastInsertId('departments','id');
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

	/**
	 * @return int
	 */
	public function getDefault_person_id()
	{
		return $this->default_person_id;
	}

	/**
	 * @return Person
	 */
	public function getDefault_person()
	{
		if ($this->default_person_id) {
			if (!$this->default_person) {
				$this->default_person = new Person($this->default_person_id);
			}
			return $this->default_person;
		}
		return null;
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

	/**
	 * @param int $int
	 */
	public function setDefault_person_id($int)
	{
		$this->default_person = new Person($int);
		$this->default_person_id = $int;
	}

	/**
	 * @param Person $person
	 */
	public function setDefault_person($person)
	{
		$this->default_person_id = $person->getId();
		$this->default_person = $person;
	}


	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	/**
	 * @return array
	 */
	public function getCategories()
	{
		if (!count($this->categories)) {
			$list = new CategoryList(array('department_id'=>$this->id));
			foreach ($list as $category) {
				$this->categories[$category->getId()] = $category;
			}
		}
		return $this->categories;
	}

	/**
	 * @param Category $category
	 * @return bool
	 */
	public function hasCategory(Category $category)
	{
		return array_key_exists($category->getId(),$this->getCategories());
	}

	/**
	 * @return bool
	 */
	public function hasCategories()
	{
		return count($this->getCategories()) ? true : false;
	}

	/**
	 * Saves a set of categories to the database
	 *
	 * Replaces the database records for the department
	 * with a new set of categories
	 *
	 * @param array|CategoryList $categories
	 */
	public function saveCategories($categories)
	{
		if ($this->id) {
			$zend_db = Database::getConnection();
			$zend_db->delete('department_categories','department_id='.$this->id);
			foreach ($categories as $category) {
				if (!$category instanceof Category) {
					$category = new Category($category);
				}

				$zend_db->insert('department_categories',
								array('department_id'=>$this->id,'category_id'=>$category->getId()));
			}
		}
	}

	/**
	 * @return array
	 */
	public function getActions()
	{
		if (!count($this->actions)) {
			$list = new ActionList(array('department_id'=>$this->id));
			foreach ($list as $action) {
				$this->actions[$action->getId()] = $action;
			}
		}
		return $this->actions;
	}

	/**
	 * @param Action $action
	 * @return bool
	 */
	public function hasAction(Action $action)
	{
		return array_key_exists($action->getId(),$this->getActions());
	}

	/**
	 * @return bool
	 */
	public function hasActions()
	{
		return count($this->getActions()) ? true : false;
	}

	/**
	 * Saves a set of actions to the database
	 *
	 * Replaces the database records for the department
	 * with a new set of actions
	 *
	 * @param array|ActionList $actions
	 */
	public function saveActions($actions)
	{
		if ($this->id) {
			$zend_db = Database::getConnection();
			$zend_db->delete('department_actions','department_id='.$this->id);
			foreach ($actions as $action) {
				if (!$action instanceof Action) {
					$action = new Action($action);
				}

				$zend_db->insert(
					'department_actions',
					array('department_id'=>$this->id,'action_id'=>$action->getId())
				);
			}
		}
	}

	/**
	 * @return UserList
	 */
	public function getUsers()
	{
		return new UserList(array('department_id'=>$this->id));
	}
}
