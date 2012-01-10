<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Label extends MongoRecord
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
				$search = array('_id'=>new MongoId($id));
				$result = $mongo->labels->findOne($search);
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('labels/unknownLabel');
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
		if (!$this->getName()) {
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
		$mongo->labels->save($this->data,array('safe'=>true));
	}

	public function delete()
	{
		$mongo = Database::getConnection();
		$mongo->labels->remove(array('_id'=>$this->getId()));
	}

	//----------------------------------------------------------------
	// Generic Getters and Setters
	//----------------------------------------------------------------
	/**
	 * @return string
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
	 * @param string $string
	 */
	public function setName($string)
	{
		$this->data['name'] = trim($string);
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * @param array $post
	 */
	public function set($post)
	{
		$this->setName($post['name']);
	}
}