<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Action extends MongoRecord
{
	public static $types = array('system','department');

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
				$result = $mongo->actions->findOne($search);
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('actions/unknownAction');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setType('department');
		}
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->getName() || !$this->getDescription()) {
			throw new Exception('missingRequiredFields');
		}

		if (!$this->getType()) {
			$this->setType('department');
		}
	}

	/**
	 * Saves this record back to the database
	 */
	public function save()
	{
		$this->validate();
		$mongo = Database::getConnection();
		$mongo->actions->save($this->data,array('safe'=>true));
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()          { return $this->get('_id');         }
	public function getName()        { return $this->get('name');        }
	public function getDescription() { return $this->get('description'); }
	public function getType()        { return $this->get('type');        }

	public function setName($s)        { $this->data['name']        = trim($s); }
	public function setDescription($s) { $this->data['description'] = trim($s); }

	/**
	 * @param string $string
	 */
	public function setType($string)
	{
		$string = trim($string);
		if (in_array($string,self::$types)) {
			$this->data['type'] = $string;
		}
	}
}