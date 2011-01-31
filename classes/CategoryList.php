<?php
/**
 * A collection class for Category objects
 *
 * This class creates a zend_db select statement.
 * ZendDbResultIterator handles iterating and paginating those results.
 * As the results are iterated over, ZendDbResultIterator will pass each desired
 * row back to this class's loadResult() which will be responsible for hydrating
 * each Category object
 *
 * Beyond the basic $fields handled, you will need to write your own handling
 * of whatever extra $fields you need
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class CategoryList extends ZendDbResultIterator
{
	/**
	 * Creates a basic select statement for the collection.
	 *
	 * Populates the collection if you pass in $fields
	 * Setting itemsPerPage turns on pagination mode
	 * In pagination mode, this will only load the results for one page
	 *
	 * @param array $fields
	 * @param int $itemsPerPage Turns on Pagination
	 * @param int $currentPage
	 */
	public function __construct($fields=null,$itemsPerPage=null,$currentPage=null)
	{
		parent::__construct($itemsPerPage,$currentPage);
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
	public function find($fields=null,$order='c.id',$limit=null,$groupBy=null)
	{
		$this->select->from(array('c'=>'categories'));

		// Finding on fields from the categories table is handled here
		if (isset($fields['name'])) {
			$this->select->where('c.name=?',$fields['name']);
		}

		// Finding on fields from other tables requires joining those tables.
		// You can handle fields from other tables by adding the joins here
		// If you add more joins you probably want to make sure that the
		// above foreach only handles fields from the categories table.
		$joins = array();

		if (isset($fields['department_id'])) {
			$joins['d'] = array('table'=>'department_categories','condition'=>'c.id=d.category_id');
			$this->select->where('d.department_id=?',$fields['department_id']);
		}

		if (isset($fields['issue_id'])) {
			$joins['i'] = array('table'=>'issue_categories','condition'=>'c.id=i.category_id');
			$this->select->where('i.issue_id=?',$fields['issue_id']);
		}

		if (isset($fields['ticket_id'])) {
			$joins['i'] = array('table'=>'issue_categories','condition'=>'c.id=i.category_id');
			$joins['x'] = array('table'=>'issues','condition'=>'i.issue_id=x.id');
			$this->select->where('x.ticket_id=?',$fields['ticket_id']);
		}

		foreach ($joins as $key=>$join) {
			$this->select->joinLeft(array($key=>$join['table']),$join['condition'],array());
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
	 * Hydrates all the Category objects from a database result set
	 *
	 * This is a callback function, called from ZendDbResultIterator.  It is
	 * called once per row of the result.
	 *
	 * @param int $key The index of the result row to load
	 * @return Category
	 */
	protected function loadResult($key)
	{
		return new Category($this->result[$key]);
	}
}
