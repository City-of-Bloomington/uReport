<?php
/**
 * A collection class for User objects
 *
 * This class creates a zend_db select statement.
 * ZendDbResultIterator handles iterating and paginating those results.
 * As the results are iterated over, ZendDbResultIterator will pass each desired
 * row back to this class's loadResult() which will be responsible for hydrating
 * each User object
 *
 * Beyond the basic $fields handled, you will need to write your own handling
 * of whatever extra $fields you need
 *
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class UserList extends ZendDbResultIterator
{
	private $columns = array('id'=>1,'person_id'=>2,'username'=>3,'password'=>4,'authenticationMethod'=>5,'department_id'=>6);

	/**
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		parent::__construct();

		if (is_array($fields)) {
			$this->find($fields);
		}
	}

	/**
	 * Populates the collection
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function find($fields=null,$order='username',$limit=null,$groupBy=null)
	{
		$this->select->from(array('u'=>'users'));

		// Finding on fields from the Users table is handled here
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				 
				 
				if (array_key_exists($key,$this->columns)
					) {
					 echo "*** after \n";
					$this->select->where("u.$key=?",$value);
				}
			}
		}

		// Finding on fields from other tables requires joining those tables.
		// You can handle fields from other tables by adding the joins here
		// If you add more joins you probably want to make sure that the
		// above foreach only handles fields from the users table.
		$joins = array();

		// Firstname, lastname, and email come from the People table
		if (isset($fields['firstname'])) {
			$joins['p'] = array('table'=>'people','condition'=>'u.id=p.user_id');
			$this->select->where('p.firstname=?',$fields['firstname']);
		}
		if (isset($fields['lastname'])) {
			$joins['p'] = array('table'=>'people','condition'=>'u.id=p.user_id');
			$this->select->where('p.lastname=?',$fields['lastname']);
		}
		if (isset($fields['email'])) {
			$joins['p'] = array('table'=>'people','condition'=>'u.id=p.user_id');
			$this->select->where('p.email=?',$fields['email']);
		}

		// To get the Role, we have to join the user_roles and roles tables
		if (isset($fields['role'])) {
			$joins['ur'] = array('table'=>'user_roles','condition'=>'u.id=ur.user_id');
			$joins['r'] = array('table'=>'roles','condition'=>'ur.role_id=r.id');
			$this->select->where('r.name=?',$fields['role']);
		}

		// Add all the joins we've created to the select
		foreach ($joins as $key=>$join) {
			$this->select->joinLeft(array($key=>$join['table']),$join['condition']);
		}



		$this->select->order($order);
		if ($limit) {
			$this->select->limit($limit);
		}
		if ($groupBy) {
			$this->select->group($groupBy);
		}
		$this->populateList();
	}

	/**
	 * Hydrates all the objects from a database result set
	 *
	 * This is a callback function, called from ZendDbResultIterator.  It is
	 * called once per row of the result.
	 *
	 * @param int $key The index of the result row to load
	 * @return User
	 */
	protected function loadResult($key)
	{
		return new User($this->result[$key]);
	}
}
