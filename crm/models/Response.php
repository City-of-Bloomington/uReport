<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Response extends ActiveRecord
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
				$zend_db = Database::getConnection();
				$sql = 'select * from labels where id=?';
				$result = $zend_db->fetchRow($sql, array($id));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('responses/unknownResponse');
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setDate('now');
		}
	}

	public function validate()
	{
		if (!$this->getDate()) { $this->setDate('now'); }
		if (!$this->getPerson_id()) { throw new Exception('response/unknownPerson'); }
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------
	public function getId()               { return parent::get('id');               }
	public function getNotes()            { return parent::get('notes');            }
	public function getDate($f=null, DateTimeZone $tz=null) { return parent::getDateData('date', $f, $tz); }

	public function setNotes($s) { $this->data['notes'] = trim($s); }
	public function setDate($d) { parent::setDateData('date', $d); }

	public function getIssue_id()         { return parent::get('issue_id');         }
	public function getContactMethod_id() { return parent::get('contactMethod_id'); }
	public function getPerson_id()        { return parent::get('person_id');        }
	public function getIssue()         { return parent::getForeignKeyObject('Issue',         'issue_id');         }
	public function getContactMethod() { return parent::getForeignKeyObject('ContactMethod', 'contactMethod_id'); }
	public function getPerson()        { return parent::getForeignKeyObject('Person',        'person_id');        }

	public function setIssue_id        ($id) { parent::setForeignKeyField('Issue',         'issue_id',         $id); }
	public function setContactMethod_id($id) { parent::setForeignKeyField('ContactMethod', 'contactMethod_id', $id); }
	public function setPerson_id       ($id) { parent::setForeignKeyField('Person',        'person_id',        $id); }
	public function setIssue        (Issue         $o) { parent::setForeignKeyObject('Issue',         'issue_id',         $o); }
	public function setContactMethod(ContactMethod $o) { parent::setForeignKeyObject('ContactMethod', 'contactMethod_id', $o); }
	public function setPerson       (Person        $o) { parent::setForeignKeyObject('Person',        'person_id',        $o); }
}