<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class CategoryGroup extends MongoRecord
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
					$search = array('name'=>(string)$id);
				}
				$result = $mongo->categoryGroups->findOne($search);
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('categoryGroups/unknownGroup');
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
		$mongo->categoryGroups->save($this->data,array('safe'=>true));

		$mongo->categories->update(
			array('group._id'=>$this->data['_id']),
			array('$set'=>array('group'=>$this->data)),
			array('upsert'=>false,'multiple'=>true,'safe'=>false)
		);
	}

	public function delete()
	{
		$mongo = Database::getConnection();
		$mongo->categoryGroups->remove(array('_id'=>$this->getId()));
	}

	//----------------------------------------------------------------
	// Getters and Setters
	//----------------------------------------------------------------
	public function getId()			{ return $this->get('_id'); }
	public function getName()		{ return $this->get('name'); }
	public function getOrder()		{ return $this->get('order'); }
	public function __toString()	{ return $this->get('name'); }

	public function setName($string)	{ $this->data['name']  = trim($string); }
	public function setOrder($int)		{ $this->data['order'] = (int)$int; }

	/**
	 * @param array $post
	 */
	public function set($post)
	{
		$fields = array('name','order');

		foreach ($fields as $field) {
			if (isset($post[$field])) {
				$set = 'set'.ucfirst($field);
				$this->$set($post[$field]);
			}
		}
	}
	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * @return CategoryList
	 */
	public function getCategories()
	{
		return new CategoryList(array('group._id'=>$this->data['_id']));
	}
}