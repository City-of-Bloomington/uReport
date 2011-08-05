<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class Category extends MongoRecord
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
				$mongo = Database::getConnection();

				if (preg_match('/[0-9a-f]{24}/',$id)) {
					$search = array('_id'=>new MongoId($id));
				}
				else {
					$search = array('name'=>(string)$id);
				}
				$result = $mongo->categories->findOne($search);
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
	}

	/**
	 * Saves this record back to the database
	 */
	public function save()
	{
		$this->validate();
		$mongo = Database::getConnection();
		$mongo->categories->save($this->data,array('safe'=>true));

		$this->updateCategoryInTicketData();
	}

	public function updateCategoryInTicketData()
	{
		$mongo = Database::getConnection();
		$mongo->tickets->update(
			array('category._id'=>$this->data['_id']),
			array('$set'=>array('category'=>$this->data)),
			array('upsert'=>false,'multiple'=>true,'safe'=>false)
		);
	}

	//----------------------------------------------------------------
	// Generic Getters
	//----------------------------------------------------------------
	/**
	 * @return string
	 */
	public function getId()
	{
		if (isset($this->data['_id'])) {
			return $this->data['_id'];
		}
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		if (isset($this->data['name'])) {
			return $this->data['name'];
		}
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		if (isset($this->data['description'])) {
			return $this->data['description'];
		}
	}

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
		if (isset($this->data['customFields'])) {
			return $this->data['customFields'];
		}
		return array();
	}

	//----------------------------------------------------------------
	// Generic Setters
	//----------------------------------------------------------------
	/**
	 * @param string $string
	 */
	public function setName($string)
	{
		$this->data['name'] = trim($string);
	}

	/**
	 * @param string $string
	 */
	public function setDescription($string)
	{
		$this->data['description'] = trim($string);
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
				$this->data['customFields'] = $customFields;
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
	 * @return string
	 */
	public function getPostingPermissionLevel()
	{
		return isset($this->data['postingPermissionLevel'])
			? $this->data['postingPermissionLevel']
			: '';
	}

	/**
	 * @param string $level
	 */
	public function setPostingPermissionLevel($level)
	{
		$this->data['postingPermissionLevel'] = $level;
	}

	/**
	 * @return string
	 */
	public function getDisplayPermissionLevel()
	{
		return isset($this->data['displayPermissionLevel'])
			? $this->data['displayPermissionLevel']
			: '';
	}

	/**
	 * @param string $level
	 */
	public function setDisplayPermissionLevel($level)
	{
		$this->data['displayPermissionLevel'] = $level;
	}

	//----------------------------------------------------------------
	// Custom Functions
	// We recommend adding all your custom code down here at the bottom
	//----------------------------------------------------------------
	public function __toString()
	{
		return $this->getName();
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