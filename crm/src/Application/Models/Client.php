<?php
/**
 * A Web Service Client authorized to POST tickets
 *
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Application\ActiveRecord;
use Application\Database;

class Client extends ActiveRecord
{
	protected $tablename = 'clients';

	protected $contactPerson;
	protected $contactMethod;

	public static function loadByApiKey($api_key)
	{
		$db = Database::getConnection();
		$sql = 'select * from clients where api_key=?';
		$result = $db->createStatement($sql)->execute([$api_key]);
		if (count($result)) {
			return new Client($result->current());
		}
		else {
			throw new \Exception('clients/unknownApiKey');
		}
	}

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
				$sql = 'select * from clients where id=?';

				$db = Database::getConnection();
				$result = $db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('clients/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->data['api_key'] = uniqid();

			if (isset($_SESSION['USER'])) {
				$this->setContactPerson($_SESSION['USER']);
			}
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

        $this->contactPerson = null;
        $this->contactMethod = null;
    }

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->getName() || !$this->getContactPerson()) {
			throw new \Exception('missingRequiredFields');
		}

		if (!$this->getApi_key()) {
			$this->data['api_key'] = uniqid();
		}
	}

	public function save()   { parent::save();   }
	public function delete() { parent::delete(); }

	//----------------------------------------------------------------
	// Generic Getters and Setters
	//----------------------------------------------------------------
	public function getId()               { return parent::get('id');               }
	public function getName()             { return parent::get('name');             }
	public function getURL()              { return parent::get('url');              }
	public function getApi_key()          { return parent::get('api_key');          }
	public function getContactPerson_id() { return parent::get('contactPerson_id'); }
	public function getContactMethod_id() { return parent::get('contactMethod_id'); }
	public function getContactPerson()    { return parent::getForeignKeyObject(__namespace__.'\Person',        'contactPerson_id'); }
	public function getContactMethod()    { return parent::getForeignKeyObject(__namespace__.'\ContactMethod', 'contactMethod_id'); }

	public function setName($s)    { parent::set('name', $s); }
	public function setURL ($s)    { parent::set('url',  $s); }
	public function setApi_key($s) { parent::set('api_key', $s); }
	public function setContactPerson_id($id)           { parent::setForeignKeyField( __namespace__.'\Person',        'contactPerson_id', $id); }
	public function setContactMethod_id($id)           { parent::setForeignKeyField( __namespace__.'\ContactMethod', 'contactMethod_id', $id); }
	public function setContactPerson(Person        $o) { parent::setForeignKeyObject(__namespace__.'\Person',        'contactPerson_id', $o);  }
	public function setContactMethod(ContactMethod $o) { parent::setForeignKeyObject(__namespace__.'\ContactMethod', 'contactMethod_id', $o);  }


	/**
	 * @param array $post
	 */
	 public function handleUpdate($post)
	 {
		$this->setName            ($post['name']);
		$this->setURL             ($post['url']);
		$this->setApi_key         ($post['api_key']);
		$this->setContactPerson_id($post['contactPerson_id']);
		$this->setContactMethod_id($post['contactMethod_id']);
	 }
	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
}
