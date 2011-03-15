<?php
/**
 * A collection class for Person objects
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class PersonList extends ZendDbResultIterator
{
	private $columns = array(
		'firstname','middlename','lastname','email','phone',
		'address','city','state','zip',
		'street_address_id','subunit_id','township','neighborhoodAssociation'
	);

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
	 * Creates the base select query that this class uses.
	 * Both find() and search() will use the same select query.
	 */
	public function createSelection()
	{
		$this->select->from(array('p'=>'people'));
	}

	/**
	 * Populates the collection, using strict matching of the requested fields
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function find($fields=null,$order='p.lastname,p.firstname',$limit=null,$groupBy=null)
	{
		$this->createSelection();

		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if (in_array($key,$this->columns)) {
					$value = trim($value);
					$this->select->where("p.$key=?",$value);
				}
			}
		}

		if (isset($fields['enteredTicket'])) {
			$count = $fields['enteredTicket'] ? '> 1' : '= 0';
			$this->select->join(array('t'=>'tickets'),
								'p.id=t.enteredByPerson_id',
								array('ticketCount'=>'count(*)'));
			$this->select->group('p.id');
			$this->select->having("ticketCount $count");

		}


		$this->runSelection($order,$limit,$groupBy);
	}

	/**
	 * Populates the collection, using loose matching of the requested fields
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function search($fields=null,$order='p.lastname,p.firstname',$limit=null,$groupBy=null)
	{
		$this->createSelection();

		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if (in_array($key,$this->columns)) {
					$value = addslashes(trim($value));
					$this->select->where("p.$key like ?","%$value%");
				}
			}
		}

		if (isset($fields['name'])) {
			$name = addslashes(trim($fields['name']));
			$this->select->where("p.firstname like '%$name%' or p.lastname like '%$name%'");
		}

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
	 * Loads a single Person object for the row returned from ZendDbResultIterator
	 *
	 * @param array $key
	 */
	protected function loadResult($key)
	{
		return new Person($this->result[$key]);
	}
}
