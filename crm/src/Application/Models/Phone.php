<?php
/**
 * @copyright 2013-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Application\ActiveRecord;
use Application\Database;

class Phone extends ActiveRecord
{
	protected $tablename = 'peoplePhones';
	protected $person;

	public static $LABELS = array('Main', 'Mobile', 'Work', 'Home', 'Fax', 'Pager', 'Other');
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
				$this->exchangeArray($id);
			}
			else {
				$zend_db = Database::getConnection();
				$sql = 'select * from peoplePhones where id=?';
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('phones/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setLabel('Other');
		}
	}

	/**
     * When repopulating with fresh data, make sure to set default
     * values on all object properties.
     *
     * @Override
     * @param array $data
     */
    public function exchangeArray($data)
    {
        parent::exchangeArray($data);

        $this->person = null;
    }

	public function validate()
	{
		if (!$this->getLabel()) { $this->setLabel('Other'); }
		if (!$this->getPerson_id()) { throw new \Exception('phones/missingPerson'); }
	}

	public function save()   { parent::save();   }
	public function delete() { parent::delete(); }

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()       { return parent::get('id');       }
	public function getNumber()   { return parent::get('number');   }
	public function getDeviceId() { return parent::get('deviceId'); }
	public function getLabel()    { return parent::get('label');    }

	public function setNumber  ($s) { parent::set('number',   $s); }
	public function setDeviceId($s) { parent::set('deviceId', $s); }
	public function setLabel   ($s) { parent::set('label',    $s); }

	public function getPerson_id() { return parent::get('person_id'); }
	public function getPerson()    { return parent::getForeignKeyObject(__namespace__.'\Person', 'person_id');      }
	public function setPerson_id($id)     { parent::setForeignKeyField (__namespace__.'\Person', 'person_id', $id); }
	public function setPerson(Person $p)  { parent::setForeignKeyObject(__namespace__.'\Person', 'person_id', $p);  }

	public function handleUpdate($post)
	{
		$fields = array('number', 'deviceId', 'label', 'person_id');
		foreach ($fields as $f) {
			if (isset($post[$f])) {
				$set = 'set'.ucfirst($f);
				$this->$set($post[$f]);
			}
		}
	}
}
