<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Phone extends ActiveRecord
{
	protected $tablename = 'phones';
	protected $allowsDelete = true;
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
				$sql = 'select * from phones where id=?';
				$result = $zend_db->fetchRow($sql, array($id));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('phones/unknownPhone');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	public function validate()
	{
		if (!$this->getPerson_id()) { throw new Exception('phones/missingPerson'); }
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()       { return parent::get('id');       }
	public function getNumber()   { return parent::get('number');   }
	public function getDeviceId() { return parent::get('deviceId'); }

	public function setNumber  ($s) { $this->data['number']   = trim($s); }
	public function setDeviceId($s) { $this->data['deviceId'] = trim($s); }

	public function getPerson_id() { return parent::get('person_id'); }
	public function getPerson()    { return parent::getForeignKeyObject('Person', 'person_id');      }
	public function setPerson_id($id)     { parent::setForeignKeyField ('Person', 'person_id', $id); }
	public function setPerson(Person $p)  { parent::setForeignKeyObject('Person', 'person_id', $p);  }
}