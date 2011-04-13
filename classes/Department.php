<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Department
{
	private $data = array();

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
				$mongo = Database::getConnection();
				$result = $mongo->departments->findOne(array('_id'=>new MongoId($id)));
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
		if (!$this->data['name'] || !$this->data['default_person']) {
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
		$mongo->departments->save($this->data,array('safe'=>true));
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------

	/**
	 * @return string Mongo's unique identifier
	 */
	public function getId()
	{
		if (isset($this->data['_id'])) {
			return (string)$this->data['_id'];
		}
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		if (isset($this->data['name'])) {
			return $this->data['name'];
		}
	}

	/**
	 * @return Person
	 */
	public function getDefaultPerson()
	{
		if ($this->data['default_person']) {
			return $this->data['default_person'];
		}
	}
	
	/**
	 * @return array
	 */
	public function getCategories()
	{
		if (isset($this->data['categories'])) {
			return $this->data['categories'];
		}
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
	 * @param Person $person
	 */
	public function setDefaultPerson($person)
	{
		$this->data['default_person'] = array(
			'id'=>$person->getId(),
			'firstname'=>$person->getFirstname(),
			'lastname'=>$person->getLastname()
		);
	}

	public function setCategories($categories)
	{
	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------

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

	/**
	 * @return array
	 */
	public function getCustomStatuses()
	{
		if (!count($this->customStatuses)) {
			if ($this->id) {
				$zend_db = Database::getConnection();
				$query = $zend_db->query(
					'select status from customStatuses where department_id=?',
					array($this->id)
				);
				$this->customStatuses = $query->fetchAll(Zend_Db::FETCH_COLUMN);
			}
		}
		return $this->customStatuses;
	}

	/**
	 * @return bool
	 */
	public function hasCustomStatuses()
	{
		return count($this->getCustomStatuses()) ? true : false;
	}

	/**
	 * Immediately saves an array of status strings to the database
	 *
	 * @param string|array $statuses
	 */
	public function saveCustomStatuses($statuses)
	{
		if ($this->id) {
			$zend_db = Database::getConnection();
			$zend_db->delete('customStatuses','department_id='.$this->id);

			if (!is_array($statuses)) {
				$statuses = explode(',',$statuses);
			}
			$this->customStatuses = $statuses;

			foreach ($statuses as $status) {
				$zend_db->insert(
					'customStatuses',
					array('department_id'=>$this->id,'status'=>$status)
				);
			}
		}
	}
}
