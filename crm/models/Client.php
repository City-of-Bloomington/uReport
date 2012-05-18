<?php
/**
 * A Web Service Client authorized to POST tickets
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Client extends ActiveRecord
{
	protected $tablename = 'clients';
	protected $allowsDelete = true;

	private $contactPerson;
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
				$sql = 'select * from clients where id=?';
				$result = $zend_db->fetchRow($sql, array($id));
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

	//----------------------------------------------------------------
	// Generic Getters and Setters
	//----------------------------------------------------------------
	public function getId()               { return parent::get('id');               }
	public function getName()             { return parent::get('name');             }
	public function getURL()              { return parent::get('url');              }
	public function getContactPerson_id() { return parent::get('contactPerson_id'); }
	public function getContactPerson()    { return parent::getForeignKeyObject('Person', 'contactPerson_id'); }

	public function setName($s) { $this->data['name'] = trim($s); }
	public function setURL ($s) { $this->data['url']  = trim($s); }
	public function setContactPerson_id($id)    { parent::setForeignKeyField( 'Person', 'contactPerson_id', $id); }
	public function setContactPerson(Person $p) { parent::setForeignKeyObject('Person', 'contactPerson_id', $p);  }

	/**
	 * @param array $post
	 */
	 public function set($post)
	 {
		$this->setName            ($post['name']);
		$this->setURL             ($post['url']);
		$this->setContactPerson_id($post['contactPerson_id']);
	 }
	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
}