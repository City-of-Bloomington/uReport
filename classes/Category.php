<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Category
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
				
				if (preg_match('/[0-9a-f]{24}/',$id)) {
					$search = array('_id'=>new MongoId($id));
				}
				else {
					$search = array('name'=>(string)$id);
				}
				$result = $mongo->categories->findOne($search);
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('categories/unknownCategory');
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
		if(!$this->data['name']) {
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
		$mongo->categories->save($this->data,array('safe'=>true));
	}
	
	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->data['_id'];
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->data['name'];
	}
	
	/**
	 * @return array
	 */
	public function getTypes()
	{
		return $this->data['types'];
	}
	
	/**
	 * @return array
	 */
	public function getProblems()
	{
		return $this->data['problems'];
	}
	
	/**
	 * @return array
	 */
	public function getCustomFields()
	{
		return $this->data['customFields'];
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
	
	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	public function __toString()
	{
		return $this->getName();
	}
}
