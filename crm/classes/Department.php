<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Department extends MongoRecord
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
				if (preg_match('/[0-9a-f]{24}/',$id)) {
					$search = array('_id'=>new MongoId($id));
				}
				else {
					$search = array('name'=>$id);
				}
				$result = $mongo->departments->findOne($search);
			}

			if ($result) {
				$this->data = $result;
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
		if (!$this->data['name']) {
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

	public function delete()
	{
		if ($this->getId()) {
			if (count($this->getPeople())) {
				throw new Exception('departments/stillHasPeople');
			}
			else {
				$mongo = Database::getConnection();
				$mongo->departments->remove(array('_id'=>$this->getId()));
			}
		}
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
			return $this->data['_id'];
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
		if (isset($this->data['defaultPerson'])) {
			return new Person($this->data['defaultPerson']);
		}
	}

	/**
	 * @return array
	 */
	public function getCustomStatuses()
	{
		if (isset($this->data['customStatuses'])) {
			return $this->data['customStatuses'];
		}
		return array();
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------

	/**
	 * @param string $string
	 */
	public function setName($string)
	{
		$this->data['name'] = trim($string);
	}

	/**
	 * Sets person data
	 *
	 * See: MongoRecord->setPersonData
	 *
	 * @param string|array|Person $person
	 */
	public function setDefaultPerson($person)
	{
		$this->setPersonData('defaultPerson',$person);
	}

	/*
	 *@param string $string
	 */
	public function setCustomStatuses($string)
	{
		$this->data['customStatuses'] = array();
		foreach (explode(',',$string) as $status) {
			$status = trim($status);
			if ($status) {
				$this->data['customStatuses'][] = $status;
			}
		}
	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	public function __toString()
	{
		return $this->getName();
	}

	/**
	 * Returns an array of category data
	 *
	 * @return array
	 */
	public function getCategories()
	{
		if (isset($this->data['categories'])) {
			return $this->data['categories'];
		}
		return array();
	}

	/**
	 * @param array $categories
	 */
	public function setCategories($categories)
	{
		$this->data['categories'] = array();

		foreach ($categories as $id) {
			try {
				$category = new Category($id);
				$this->data['categories'][] = array(
					'_id'=>$category->getId(),
					'name'=>$category->getName()
				);
			}
			catch (Exception $e) {
				// Just ignore the bad ones
			}
		}
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasCategory($name)
	{
		foreach ($this->getCategories() as $category) {
			if ($name == $category['name']) {
				return true;
			}
		}
	}

	/**
	 * Returns an array of action data
	 *
	 * @return array
	 */
	public function getActions()
	{
		if (isset($this->data['actions'])) {
			return $this->data['actions'];
		}
		return array();
	}

	/**
	 * @param array $actions
	 */
	public function setActions($actions)
	{
		$this->data['actions'] = array();

		foreach ($actions as $id) {
			try {
				$action = new Action($id);
				$this->data['actions'][] = array(
					'_id'=>$action->getId(),
					'name'=>$action->getName()
				);
			}
			catch (Exception $e) {
				// Just ignore the bad ones
			}
		}
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasAction($name)
	{
		foreach ($this->getActions() as $action) {
			if ($name == $action['name']) {
				return true;
			}
		}
	}

	/**
	 * @return UserList
	 */
	public function getPeople()
	{
		if (isset($this->data['_id'])) {
			return new PersonList(array('department._id'=>$this->data['_id']));
		}
		return array();
	}
}
