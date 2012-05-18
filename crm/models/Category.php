<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Category extends ActiveRecord
{
	protected $tablename = 'categories';

	private $department;
	private $categoryGroup;

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
				$sql = ctype_digit($id)
					? 'select * from categories where id=?'
					: 'select * from categories where name=?';

				$result = $zend_db->fetchRow($sql, array($id));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('categories/unknownCategory');
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
		// Check for required fields here.  Throw an exception if anything is missing.
		if(!$this->data['name']) {
			throw new Exception('missingRequiredFields');
		}

		if (!$this->getData('group.name')) {
			throw new Exception('categories/missingGroup');
		}
	}

	//----------------------------------------------------------------
	// Getters and Setters
	//----------------------------------------------------------------
	public function __toString()                { return parent::get('name');                   }
	public function getId()                     { return parent::get('id');                     }
	public function getName()                   { return parent::get('name');                   }
	public function getDepartment_id()          { return parent::get('department_id';           }
	public function getCategoryGroup_id()       { return parent::get('categoryGroup_id';        }
	public function getDescription()            { return parent::get('description');            }
	public function getPostingPermissionLevel() { return parent::get('postingPermissionLevel'); }
	public function getDisplayPermissionLevel() { return parent::get('displayPermissionLevel'); }

	public function setName                  ($s) { $this->data['name']                   = trim($s); }
	public function setDescription           ($s) { $this->data['description']            = trim($s); }
	public function setPostingPermissionLevel($s) { $this->data['postingPermissionLevel'] = trim($s); }
	public function setDisplayPermissionLevel($s) { $this->data['displayPermissionLevel'] = trim($s); }

	/**
	 * @param array $post
	 */
	public function set($post)
	{
		$this->setName                  ($post['name']);
		$this->setDescription           ($post['description']);
		$this->setDepartment_id         ($post['department_id']);
		$this->setCategoryGroup_id      ($post['categoryGroup_id']);
		$this->setPostingPermissionLevel($post['postingPermissionLevel']);
		$this->setDisplayPermissionLevel($post['displayPermissionLevel']);
		$this->setCustomFields          ($post['custom_fields']);
	}

	public function getDepartment()    { return parent::getForeignKeyObject('Department',    'department_id');    }
	public function getCategoryGroup() { return parent::getForeignKeyObject('CategoryGroup', 'categoryGroup_id'); }
	public function setDepartment_id($id)              { parent::setForeignKeyField('Department',     'department_id',    $id); }
	public function setCategoryGroup_id($id)           { parent::setForeignKeyField('CategoryGroup',  'categoryGroup_id', $id); }
	public function setDepartment(Department $d)       { parent::setForeignKeyObject('Department',    'department_id',    $d);  }
	public function setCategoryGroup(CategoryGroup $g) { parent::setForeignKeyObject('CategoryGroup', 'categoryGroup_id', $g);  }

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Returns a PHP array representing the description of custom fields
	 *
	 * The category holds the description of the custom fields desired
	 * $customFields = array(
	 *		array('name'=>'','type'=>'','label'=>'','values'=>array())
	 * )
	 * Name and Label are required.
	 * Anything without a type will be rendered as type='text'
	 * If type is select, radio, or checkbox, you must provide values
	 *		for the user to choose from
	 *
	 * @return array
	 */
	public function getCustomFields()
	{
		return json_decode(parent::get('customFields'));
	}

	/**
	 * Loads a valid JSON string describing the custom fields
	 *
	 * The category holds the description of the custom fields desired
	 * $json = [
	 *		{'name':'','type':'','label':'','values':['',''])
	 * ]
	 * Name and Label are required.
	 * Anything without a type will be rendered as type='text'
	 * If type is select, radio, or checkbox, you must provide values
	 *		for the user to choose from
	 *
	 * @param string $json
	 */
	public function setCustomFields($json=null)
	{
		$json = trim($json);
		$customFields = '';
		if ($json) {
			$customFields = json_decode($json);
			if (is_array($customFields)) {
				$this->data['customFields'] = $json;
			}
			else {
				throw new JSONException(json_last_error());
			}
		}
		else {
			unset($this->data['customFields']);
		}
	}

	/**
	 * @param Person $person
	 * @return bool
	 */
	public function allowsDisplay($person)
	{
		if (!$person instanceof Person) {
			return $this->getDisplayPermissionLevel()=='anonymous';
		}
		elseif ($person->getRole()!='Staff' && $person->getRole()!='Administrator') {
			return in_array(
				$this->getDisplayPermissionLevel(),
				array('public','anonymous')
			);
		}
		return true;
	}

	/**
	 * @return bool
	 */
	public function allowsPosting($person)
	{
		if (!$person instanceof Person) {
			return $this->getPostingPermissionLevel()=='anonymous';
		}
		elseif ($person->getRole()!='Staff' && $person->getRole()!='Administrator') {
			return in_array(
				$this->getPostingPermissionLevel(),
				array('public','anonymous')
			);
		}
		return true;
	}
}

class JSONException extends Exception
{
	public function __construct($message, $code=0, Exception $previous=null)
	{
		switch ($message) {
			case JSON_ERROR_NONE:
				$this->message = 'No errors';
			break;
			case JSON_ERROR_DEPTH:
				$this->message = 'Maximum stack depth exceeded';
			break;
			case JSON_ERROR_STATE_MISMATCH:
				$this->message = 'Underflow or the modes mismatch';
			break;
			case JSON_ERROR_CTRL_CHAR:
				$this->message = 'Unexpected control character found';
			break;
			case JSON_ERROR_SYNTAX:
				$this->message = 'Syntax error, malformed JSON';
			break;
			case JSON_ERROR_UTF8:
				$this->message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
			break;
			default:
				$this->message = 'Unknown JSON error';
			break;
		}
	}
}