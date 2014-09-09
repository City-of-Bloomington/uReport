<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;
use Blossom\Classes\ActiveRecord;
use Blossom\Classes\Database;

class Department extends ActiveRecord
{
	protected $tablename = 'departments';

	protected $defaultPerson;
	private $categories = array();
	private $actions    = array();

	private $categoriesUpdated = false;
	private $actionsUpdated    = false;
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
				$sql = ActiveRecord::isId($id)
					? 'select * from departments where id=?'
					: 'select * from departments where name=?';
				$result = $zend_db->createStatement($sql)->execute([$id]);
				if (count($result)) {
					$this->exchangeArray($result->current());
				}
				else {
					throw new \Exception('departments/unknownDepartment');
				}
			}
		}
		else {
			// This is where the code goes to generate a new, empty instance.
			// Set any default values for properties that need it here
			$this->setDefaultPerson_id(1);
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

        $this->defaultPerson = null;
        $this->categories = [];
        $this->actions    = [];
        $this->categoriesUpdated = false;
        $this->actionsUpdated    = false;
	}

	/**
	 * Throws an exception if anything's wrong
	 * @throws Exception $e
	 */
	public function validate()
	{
		if (!$this->data['name']) {
			throw new \Exception('missingRequiredFields');
		}

		if (!$this->getDefaultPerson_id()) {
			$this->setDefaultPerson_id(1);
		}
	}

	public function save()
	{
		parent::save();
		if ($this->categoriesUpdated) $this->saveCategories(array_keys($this->getCategories()));
		if ($this->actionsUpdated)    $this->saveActions   (array_keys($this->getActions()));
	}


	public function delete()
	{
		if ($this->isSafeToDelete()) {
			$zend_db = Database::getConnection();
			$zend_db->query('delete from department_actions where department_id=?', array($this->getId()));
			parent::delete();
		}
		else {
			throw new \Exception('departments/foreignKeyViolation');
		}
	}

	//----------------------------------------------------------------
	// Generic Getters & Setters
	//----------------------------------------------------------------
	public function __toString()          { return parent::get('name');             }
	public function getId()               { return parent::get('id');               }
	public function getName()             { return parent::get('name');             }
	public function getDefaultPerson_id() { return parent::get('defaultPerson_id'); }
	public function getDefaultPerson()    { return parent::getForeignKeyObject(__namespace__.'\Person', 'defaultPerson_id'); }

	public function setName($s)  { parent::set('name', $s); }
	public function setDefaultPerson_id($id)    { parent::setForeignKeyField( __namespace__.'\Person', 'defaultPerson_id', $id); }
	public function setDefaultPerson(Person $p) { parent::setForeignKeyObject(__namespace__.'\Person', 'defaultPerson_id', $p);  }

	/**
	 * Handler for Controller::update action
	 *
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		$this->setName($post['name']);
		if ($_POST['defaultPerson_id']) {
			$this->setDefaultPerson_id($post['defaultPerson_id']);
		}

		isset($post['categories'])
			? $this->setCategories(array_keys($post['categories']))
			: $this->setCategories(array());

		isset($post['actions'])
			? $this->setActions(array_keys($post['actions']))
			: $this->setActions(array());
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Returns an array of Category objects, indexed by Id
	 * @return array
	 */
	public function getCategories()
	{
		if (!count($this->categories) && $this->getId()) {
			$table = new CategoryTable();
			$list = $table->find(['department_id'=>$this->getId()]);
			foreach ($list as $c) {
				$this->categories[$c->getId()] = $c;
			}
		}
		return $this->categories;
	}

	/**
	 * Updates the categories, but does not save them to the database
	 *
	 * @param array $category_ids
	 */
	public function setCategories($category_ids)
	{
		$this->categoriesUpdated = true;
		$this->categories = array();
		foreach ($category_ids as $id) {
			$c = new Category($id);
			$this->categories[$c->getId()] = $c;
		}
	}

	/**
	 * Saves new categories directly to the database
	 *
	 * @param array $category_ids
	 */
	public function saveCategories($category_ids)
	{
		if ($this->getId()) {
			$this->categories = array();
			$zend_db = Database::getConnection();
			$zend_db->query('delete from department_categories where department_id=?')->execute([$this->getId()]);

			$query = $zend_db->createStatement('insert into department_categories (department_id, category_id) values(?, ?)');
			foreach ($category_ids as $id) {
				$query->execute([$this->getId(), $id]);
				try {
				}
				catch (\Exception $e) {
					// Just ignore the bad ones
				}
			}
		}
	}

	/**
	 * @param Category $category
	 * @return bool
	 */
	public function hasCategory(Category $category)
	{
		if ($this->getId()) {
			return in_array($category->getId(), array_keys($this->getCategories()));
		}
	}

	/**
	 * Returns an array of Action objects, indexed by Id
	 *
	 * @return array
	 */
	public function getActions()
	{
		if (!count($this->actions)  && $this->getId()) {
            $table = new ActionTable();
			$list = $table->find(['department_id'=>$this->getId()]);
			foreach ($list as $action) {
				$this->actions[$action->getId()] = $action;
			}
		}
		return $this->actions;
	}

	/**
	 * Updates the actions, but does not save them to the database
	 *
	 * @param array $action_ids
	 */
	public function setActions($action_ids)
	{
		$this->actionsUpdated = true;
		$this->actions = array();
		foreach ($action_ids as $id) {
			$a = new Action($id);
			$this->actions[$a->getId()] = $a;
		}
	}

	/**
	 * Saves new Actions directly to the database
	 *
	 * @param array $action_ids
	 */
	public function saveActions($action_ids)
	{
		if ($this->getId()) {
			$this->actions = array();
			$zend_db = Database::getConnection();
			$zend_db->query('delete from department_actions where department_id=?')->execute([$this->getId()]);

			$query = $zend_db->createStatement('insert into department_actions set department_id=?, action_id=?');
			foreach ($action_ids as $id) {
                $query->execute([$this->getId(), (int)$id]);
			}
		}
	}

	/**
	 * @param Action $action
	 * @return bool
	 */
	public function hasAction(Action $action)
	{
		return in_array($action->getId(), array_keys($this->getActions()));
	}

	/**
	 * @return PersonList
	 */
	public function getPeople()
	{
		if ($this->getId()) {
            $table = new PersonTable();
			return $table->find( ['department_id'=>$this->getId()] );
		}
	}

	/**
	 * @return bool
	 */
	public function isSafeToDelete()
	{
		if (!$this->getId()) { return false; }

		// Because getCategories and getPeople filter out
		// categories and people that the user is not permitted to see,
		// we cannot use those functions for this test.
		// We need to absolutely know if there is any foreign key
		// violation before we delete.
		$zend_db = Database::getConnection();
        $result = $zend_db->query('select count(*) as c from categories where department_id=?')->execute([$this->getId()]);
        $row = $result->current();
		if ($row['c']) { return false; }

		$result = $zend_db->query('select count(*) as c from department_categories where department_id=?')->execute([$this->getId()]);
        $row = $result->current();
        if ($row['c']) { return false; }

        $result = $zend_db->query('select count(*) as c from people where department_id=?')->execute([$this->getId()]);
        $row = $result->current();
        if ($row['c']) { return false; }

		return true;
	}
}
