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
				/*
				foreach ($result as $field=>$value) {
					if ($value) {
						$this->$field = $value;
					}
				}
				*/
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
		if (isset($this->data['default_person'])) {
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
	
	/**
	 * @return array
	 */
	public function getCustomStatuses()
	{
		if (isset($this->data['customStatuses'])) {
			return $this->data['castomStatuses'];
		}
	}
	/**
	 * @return array
	 */
	public function getActions()
	{
		if (isset($this->data['actions'])) {
			return $this->data['actions'];
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
		$this->data['name'] = trim($string);
	}

	/**
	 * @param Person $person
	 */
	public function setDefaultPerson($person)
	{
		$this->data['default_person'] = array(
			'_id'=>$person->getId(),
			'firstname'=>$person->getFirstname(),
			'middlename'=>$person->getMiddlename(),			
			'lastname'=>$person->getLastname(),
			'email'=>$person->getEmail()
		);
	}

	public function setCategories($categories)
	{
		if($categories && is_array($categories)){
			$mongo = Database::getConnection();
			$cats = array();			
			foreach ($categories as $category_id) {
				try{
					$result = $mongo->categories->findOne(array('_id'=>new MongoId($category_id)));				
					if($result){
						$cats[] = $result;
					}
				}catch($exception ex){}
			}
			$this->data['categories']= $cats;				
		}

	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------

	/**
	 * @param Category $category
	 * @return bool
	 */
	/*
	public function hasCategory(Category $category)
	{
		if(isset($this->data['categories'])){
			return in_array($category->getId(),$this->getCategories());	
		}
		return false;
	}
	*/
	/**
	 * @return bool
	 */
	public function hasCategories()
	{
		return count($this->getCategories()) ? true : false;
	}

	/**
	 * @return array
	 */
	public function getActions()
	{
		if (isset($this->data['actions')) {
			return $this->data['actions'];
		}
	}

	/**
	 * @param Action $action
	 * @return bool
	 */
	public function hasAction(Action $action)
	{
		return array_key_exists($action,$this->getActions());
	}

	/**
	 * @return bool
	 */
	public function hasActions()
	{
		return count($this->getActions()) ? true : false;
	}


	/**
	 * @return UserList
	 */
	public function getUsers()
	{
		return new UserList(array('department_id'=>$this->data['_id']));
	}

	/**
	 * @return array
	 */
	public function getCustomStatuses()
	{
		if (isset($this->data['customStatuses'])) {
			return $this->data['customStatuses'];
		}
	}

	/**
	 * @return bool
	 */
	public function hasCustomStatuses()
	{
		return count($this->getCustomStatuses()) ? true : false;
	}

}
