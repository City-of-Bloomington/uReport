<?php
/**
 * Represents a saved URL for a user
 *
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\ActiveRecord;
use Application\Database;

class Bookmark extends ActiveRecord
{
	protected $tablename = 'bookmarks';

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
				$sql = 'select * from bookmarks where id=?';

				$zend_db = Database::getConnection();
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('bookmarks/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setPerson_id($_SESSION['USER']->getId());
		}
	}

	public function validate()
	{
		if (!$this->getType()) {
			$this->setType('search');
		}
		if (!$this->getPerson_id()) {
			$this->setPerson_id($_SESSION['USER']->getId());
		}

		if (!$this->getRequestUri()) {
			throw new \Exception('missingRequiredFields');
		}
	}

	public function save() { parent::save(); }

	public function delete()
	{
		if ($this->getPerson_id() == $_SESSION['USER']->getId()) {
			parent::delete();
		}
	}
	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function getId()         { return parent::get('id');          }
	public function getType()       { return parent::get('type');        }
	public function getRequestUri() { return parent::get('requestUri');  }
	public function getPerson_id()  { return parent::get('person_id');   }
	public function getPerson()     { return parent::getForeignKeyObject(__namespace__.'\Person', 'person_id'); }
	public function getName() {
		$name = parent::get('name');
		return $name ? $name : $this->getRequestUri();
	}

	public function setName($s)          { parent::set('name',       $s); }
	public function setType($s)          { parent::set('type',       $s); }
	public function setRequestUri($s)    { parent::set('requestUri', $s); }
	public function setPerson_id($id)    { parent::setForeignKeyField( __namespace__.'\Person', 'person_id', $id); }
	public function setPerson(Person $o) { parent::setForeignKeyObject(__namespace__.'\Person', 'person_id', $o); }

	public function handleUpdate($post)
	{
		if (!empty($post['name'])) { $this->setName($post['name']); }
		$this->setType($post['type']);
		$this->setRequestUri($post['requestUri']);
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getFullUrl()
	{
		$protocol = $_SERVER['SERVER_PORT']==443 ? 'https://' : 'http://';
		return $protocol.$_SERVER['SERVER_NAME'].$this->getRequestUri();
	}
}
