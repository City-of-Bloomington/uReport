<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class CategoryGroup extends ActiveRecord
{
	protected $tablename = 'categoryGroups';

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
					? 'select * from categoryGroups where id=?'
					: 'select * from categoryGroups where name=?';
				$result = $zend_db->fetchRow($sql, array($id));
			}

			if ($result) {
				$this->data = $result;
			}
			else {
				throw new Exception('categoryGroups/unknownGroup');
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

	public function save()   { parent::save();   }
	public function delete() { parent::delete(); }

	//----------------------------------------------------------------
	// Getters and Setters
	//----------------------------------------------------------------
	public function getId()			{ return parent::get('id');       }
	public function getName()		{ return parent::get('name');     }
	public function getOrdering()   { return parent::get('ordering'); }
	public function __toString()	{ return parent::get('name');     }

	public function setName    ($s) { parent::set('name',     $s); }
	public function setOrdering($s)	{ parent::set('ordering', $s); }

	/**
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		$this->setName    ($post['name']);
		$this->setOrdering($post['ordering']);
	}
	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * @return CategoryList
	 */
	public function getCategories()
	{
		return new CategoryList(array('categoryGroup_id'=>$this->getId()));
	}
}