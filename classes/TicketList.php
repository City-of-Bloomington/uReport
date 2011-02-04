<?php
/**
 * A collection class for Ticket objects
 *
 * This class creates a zend_db select statement.
 * ZendDbResultIterator handles iterating and paginating those results.
 * As the results are iterated over, ZendDbResultIterator will pass each desired
 * row back to this class's loadResult() which will be responsible for hydrating
 * each Ticket object
 *
 * Beyond the basic $fields handled, you will need to write your own handling
 * of whatever extra $fields you need
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class TicketList extends ZendDbResultIterator
{
	private $columns = array(
		'person_id','location',
		'street_address_id','subunit_id',
		'neighborhoodAssociation','township'
	);

	private $issueColumns = array(
		'issueType_id','constituent_id','contactMethod_id','case_number'
	);

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
	 * Creates the base select query that this class uses.
	 * Both find() and search() will use the same select query.
	 */
	public function createSelection()
	{
		$this->select->from(array('t'=>'tickets'));
	}

	/**
	 * Populates the collection
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function find($fields=null,$order='t.date desc',$limit=null,$groupBy=null)
	{
		$this->createSelection();

		if (count($fields)) {
			foreach ($this->columns as $column) {
				if (array_key_exists($column,$fields)) {
					$fields[$column] = trim($fields[$column]);
					if ($fields[$column]) {
						$this->select->where("t.$column=?",$fields[$column]);
					}
				}
			}

			if (count(array_intersect(array_keys($fields),$this->issueColumns))) {
				foreach ($this->issueColumns as $column) {
					if (isset($fields[$column])) {
						$fields[$column] = trim($fields[$column]);
						if ($fields[$column]) {
							$this->select->where("i.$column=?",$fields[$column]);
						}
					}
				}
			}

			if (isset($fields['category_id'])) {
				$this->select->where('c.category_id=?',$fields['category_id']);
			}
		}

		$this->doJoins($fields);
		$this->runSelection($order,$limit,$groupBy);
	}

	/**
	 * Populates the collection
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function search($fields=null,$order='t.date desc',$limit=null,$groupBy=null)
	{
		$this->createSelection();

		// Finding on fields from the tickets table is handled here
		if (count($fields)) {
			foreach ($this->columns as $column) {
				if (array_key_exists($column,$fields)) {
					$fields[$column] = trim($fields[$column]);
					if ($fields[$column]) {
						if (in_array($column,array('person_id','street_address_id','subunit_id'))) {
							$this->select->where("t.$column=?",$fields[$column]);
						}
						else {
							$this->select->where("t.$column like ?","%{$fields[$column]}%");
						}
					}
				}
			}

			if (count(array_intersect(array_keys($fields),$this->issueColumns))) {
				foreach ($this->issueColumns as $column) {
					if (isset($fields[$column])) {
						$fields[$column] = trim($fields[$column]);
						if ($fields[$column]) {
							$this->select->where("i.$column=?",$fields[$column]);
						}
					}
				}
			}

			if (isset($fields['category_id']) && $fields['category_id']) {
				$this->select->where('c.category_id=?',$fields['category_id']);
			}
		}

		$this->doJoins($fields);
		$this->runSelection($order,$limit,$groupBy);
	}

	/**
	 * Adds the order, limit, and groupBy to the select, then sends the select to the database
	 *
	 * @param string $order
	 * @param string $limit
	 * @param string $groupBy
	 */
	private function runSelection($order,$limit=null,$groupBy=null)
	{
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
	 * Adds any needed joins to this->select
	 *
	 * Finding on fields from other tables requires joining those tables.
	 * You can handle fields from other tables by adding the joins here
	 * Pass in the fields that are currently being requested
	 *
	 * @param array $fields
	 */
	public function doJoins($fields)
	{
		$joins = array();

		if (count(array_intersect(array_keys($fields),$this->issueColumns))) {
			$joins['i'] = array('table'=>'issues','condition'=>'t.id=i.ticket_id');
		}

		if (isset($fields['category_id'])) {
			$joins['i'] = array('table'=>'issues','condition'=>'t.id=i.ticket_id');
			$joins['c'] = array('table'=>'issue_categories','condition'=>'i.id=c.issue_id');
		}

		foreach ($joins as $key=>$join) {
			$this->select->joinLeft(array($key=>$join['table']),$join['condition'],array());
		}
	}

	/**
	 * Hydrates all the Ticket objects from a database result set
	 *
	 * This is a callback function, called from ZendDbResultIterator.  It is
	 * called once per row of the result.
	 *
	 * @param int $key The index of the result row to load
	 * @return Ticket
	 */
	protected function loadResult($key)
	{
		return new Ticket($this->result[$key]);
	}
}
