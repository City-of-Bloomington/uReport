<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Action extends ActiveRecord
{
	protected $tablename = 'actions';
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
				$zend_db = Database::getConnection();
				$sql = ActiveRecord::isId($id)
					? 'select * from actions where id=?'
					: 'select * from actions where name=?';
				$result = $zend_db->fetchRow($sql, array($id));
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

	public function save() { parent::save(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function __toString()     { return parent::get('name');        }
	public function getId()          { return parent::get('id');          }
	public function getName()        { return parent::get('name');        }
	public function getDescription() { return parent::get('description'); }
	public function getType()        { return parent::get('type');        }

	public function setName($s)        { parent::set('name',        $s); }
	public function setDescription($s) { parent::set('description', $s); }

	/**
	 * @param string $string
	 */
	public function setType($string)
	{
		$string = trim($string);
		if (in_array($string, self::$types)) { $this->data['type'] = $string; }
	}

	/**
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		$this->setName($post['name']);
		$this->setDescription($post['description']);
		$this->setType($post['type']);
	}
}