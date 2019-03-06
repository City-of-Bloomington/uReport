<?php
/**
 * @copyright 2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;
namespace Application\Models;

use Application\ActiveRecord;
use Application\Database;

class ResponseTemplate extends ActiveRecord
{
	protected $tablename = 'category_action_responses';

	protected $category;
	protected $action;

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
				$sql = 'select * from category_action_responses where id=?';
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('responseTemplates/unknown');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
		}
	}

	public function validate()
	{
        if (!$this->getCategory_id() || !$this->getAction_id()) {
            throw new \Exception('missingRequiredFields');
        }
	}

	public function save()   { parent::save(); }
	public function delete() { parent::delete(); }

	//----------------------------------------------------------------
	// Generic Getters and Setters
	//----------------------------------------------------------------
	public function getId         () { return parent::get('id'         ); }
	public function getTemplate   () { return parent::get('template'   ); }
	public function getReplyEmail () { return parent::get('replyEmail' ); }
	public function getCategory_id() { return parent::get('category_id'); }
	public function getAction_id  () { return parent::get('action_id'  ); }
	public function getCategory   () { return parent::getForeignKeyObject(__namespace__.'\Category', 'category_id'); }
	public function getAction     () { return parent::getForeignKeyObject(__namespace__.'\Action',   'action_id'  ); }

	public function setTemplate    ($s) { parent::set('template',    $s); }
	public function setReplyEmail  ($s) { parent::set('replyEmail',  $s); }
	public function setCategory_id ($i) { parent::setForeignKeyField(__namespace__.'\Category', 'category_id', $i); }
	public function setAction_id   ($i) { parent::setForeignKeyField(__namespace__.'\Action',   'action_id',   $i); }
	public function setCategory (Category $o) { parent::setForeignKeyObject(__namespace__.'\Category', 'category_id', $o); }
	public function setAction   (Action   $o) { parent::setForeignKeyObject(__namespace__.'\Action',   'action_id',   $o); }

	public function handleUpdate(array $post)
	{
        $fields = ['category_id', 'action_id', 'template', 'replyEmail'];
        foreach ($fields as $f) {
            $set = 'set'.ucfirst($f);
            $this->$set($post[$f]);
        }
	}
}
