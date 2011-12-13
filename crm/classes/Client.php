<?php
/**
 * A Web Service Client authorized to POST tickets
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Client extends MongoRecord
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
				$result = $mongo->clients->findOne($search);
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('clients/unknownClient');
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
		if (!$this->getName() || !$this->getContactPerson()) {
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
		$mongo->clients->save($this->data,array('safe'=>true));
	}

	public function delete()
	{
		$mongo = Database::getConnection();
		$mongo->clients->remove(array('_id'=>$this->getId()));
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

	/**
	 * @return string
	 */
	public function getURL()
	{
		if (isset($this->data['url'])) {
			return $this->data['url'];
		}
	}

	/**
	 * @param string $string
	 */
	public function setURL($string)
	{
		$url = new URL($string);
		$this->data['url'] = $url->getURL();
	}

	/**
	 * @return Person
	 */
	public function getContactPerson()
	{
		if (isset($this->data['contactPerson'])) {
			return new Person($this->data['contactPerson']);
		}
	}

	/**
	 * Sets person data
	 *
	 * See: MongoRecord->setPersonData
	 *
	 * @param string|array|Person $person
	 */
	public function setContactPerson($person)
	{
		$this->setPersonData('contactPerson',$person);
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
		$this->setURL($post['url']);
		$this->setContactPerson($post['contactPerson_id']);
	 }
}