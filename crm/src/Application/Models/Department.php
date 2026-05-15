<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\ActiveRecord;
use Application\Database;

class Department extends ActiveRecord
{
	public const TABLENAME = 'departments';

	protected $defaultPerson;

	/**
	 * Populates the object with data
	 *
	 * Passing in an associative array of data will populate this object without
	 * hitting the database.
	 *
	 * Passing in a scalar will load the data from the database.
	 * This will load all fields in the table as properties of this class.
	 * You may want to replace this with, or add your own extra, custom loading
	 */
	public function __construct($id=null)
	{
		if ($id) {
			if (is_array($id)) {
				$this->exchangeArray($id);
			}
			else {
				$sql = ActiveRecord::isId($id)
					? 'select * from departments where id=?'
					: 'select * from departments where name=?';
				$result = Database::query($sql, [$id]);
				if (count($result)) {
					$this->exchangeArray($result[0]);
				}
				else {
					throw new \Exception('departments/unknown');
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
	}

	/**
	 * Throws an exception if anything's wrong
	 *
	 * @throws \Exception
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

	public function save() { parent::save(); }

	public function delete()
	{
		if ($this->isSafeToDelete()) {
			Database::execute('delete from department_actions where department_id=?', [$this->getId()]);
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
	public function getName()             { return parent::get('name');             }
	public function getDefaultPerson_id() { return parent::get('defaultPerson_id'); }
	public function getDefaultPerson()    { return parent::getForeignKeyObject(__namespace__.'\Person', 'defaultPerson_id'); }

	public function setName($s)  { parent::set('name', $s); }
	public function setDefaultPerson_id($id)    { parent::setForeignKeyField( __namespace__.'\Person', 'defaultPerson_id', $id); }
	public function setDefaultPerson(Person $p) { parent::setForeignKeyObject(__namespace__.'\Person', 'defaultPerson_id', $p);  }

	/**
	 * Handler for Controller::update action
	 *
	 * This function calls save() automatically.  There is no
	 * need to call save() after calling this function.
	 *
	 * @param array $post
	 */
	public function handleUpdate($post)
	{
		$this->setName($post['name']);
		if ($_POST['defaultPerson_id']) {
			$this->setDefaultPerson_id($post['defaultPerson_id']);
		}
		$this->save();

		isset($post['categories'])
			? $this->saveCategories(array_keys($post['categories']))
			: $this->saveCategories([]);

		isset($post['actions'])
			? $this->saveActions(array_keys($post['actions']))
			: $this->saveActions([]);
	}

	//----------------------------------------------------------------
	// Custom Functions
	//----------------------------------------------------------------
	/**
	 * Returns an array of Category objects, indexed by Id
	 *
	 * @param  array $search  Additional fields to search on
	 * @return array          An array of Category objects
	 */
	public function getCategories(array $search=[])
	{
        $id         = $this->getId();
        $categories = [];

        if ($id) {
            $search['department_id'] = $id;

			$table   = new CategoryTable();
			$list    = $table->find($search);
			foreach ($list as $c) {
                $categories[$c->getId()] = $c;
            }
		}
		return $categories;
	}

	/**
	 * Saves new categories directly to the database
	 */
	public function saveCategories(array $category_ids)
	{
        $department_id = $this->getId();
		if ($department_id) {
			$pdo = Database::getConnection();
			Database::execute('delete from department_categories where department_id=?', [$department_id]);

			$query = $pdo->prepare('insert into department_categories (department_id, category_id) values(?, ?)');
			foreach ($category_ids as $id) {
				$query->execute([$department_id, $id]);
			}
		}
	}

	/**
	 * Returns an array of Action objects, indexed by Id
	 */
	public function getActions(): array
	{
        $department_id = $this->getId();
        $actions       = [];

        if ($department_id) {
            $table = new ActionTable();
			$list  = $table->find(['department_id'=>$department_id]);
			foreach ($list as $a) {
                $actions[$a->getId()] = $a;
            }
		}
		return $actions;
	}

	/**
	 * Saves new Actions directly to the database
	 */
	public function saveActions(array $action_ids)
	{
        $department_id = $this->getId();
		if ($department_id) {
			$pdo = Database::getConnection();
			Database::query('delete from department_actions where department_id=?', [$department_id]);

			$query = $pdo->prepare('insert into department_actions set department_id=?, action_id=?');
			foreach ($action_ids as $id) {
                $query->execute([$this->getId(), (int)$id]);
			}
		}
	}

	public function getPeople(): array
	{
        $people = [];
		if ($this->getId()) {
            $table = new PersonTable();
			$list  = $table->find(['department_id' => $this->getId()]);
            foreach ($list as $p) { $people[] = $p; }
		}
		return $people;
	}

	public function isSafeToDelete(): bool
	{
		if (!$this->getId()) { return false; }

		// Because getCategories and getPeople filter out
		// categories and people that the user is not permitted to see,
		// we cannot use those functions for this test.
		// We need to absolutely know if there is any foreign key
		// violation before we delete.
		$fk = ['categories', 'department_categories', 'people'];
		foreach ($fk as $table) {
			$res = Database::query("select count(*) as c from $table where department_id=?", [$this->getId()]);
			if ($res[0]['c']) {
				return false;
			}
		}
		return true;
	}
}
