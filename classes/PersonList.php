<?php
/**
 * A collection class for Person objects
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class PersonList extends MongoResultIterator
{
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
	 * Populates the collection, using strict matching of the requested fields
	 *
	 * @param array $fields
	 * @param array $order
	 * @param int $limit
	 */
	public function find($fields=null,$order=array('lastname'=>1,'firstname'=>1),$limit=null)
	{
		$search = array();
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					$search[$key] = $value;
				}
			}
		}
		if (count($search)) {
			$this->cursor = $this->mongo->people->find($search);
		}
		else {
			$this->cursor = $this->mongo->people->find();
		}
		if ($order) {
			$this->cursor->sort($order);
		}
		if ($limit) {
			$this->cursor->limit($limit);
		}
	}
	
	/**
	 * Populates the collection, using regular expressions for matching
	 *
	 * @param array $fields
	 * @param array $order
	 * @param int $limit
	 */
	public function search($fields=null,$order=array('lastname'=>1,'firstname'=>1),$limit=null)
	{
		$search = array();
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					$search[$key] = $value;
				}
			}
		}
	}

	/**
	 * Loads a single Person object for the row returned from ZendDbResultIterator
	 *
	 * @param array $key
	 */
	protected function loadResult($data)
	{
		return new Person($data);
	}
}
