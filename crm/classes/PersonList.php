<?php
/**
 * A collection class for Person objects
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
	 */
	public function find($fields=null,$order=array('lastname'=>1,'firstname'=>1))
	{
		$search = array();
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					$search[$key] = $value;
				}
			}
		}
		$this->runSearch($search,$order);
	}

	/**
	 * Populates the collection, using regular expressions for matching
	 *
	 * @param array $fields
	 * @param array $order
	 */
	public function search($fields=null,$order=array('lastname'=>1,'firstname'=>1))
	{
		$search = array();
		if (isset($fields['query'])) {
			$regex = new MongoRegex("/$fields[query]/i");
			$search = array('$or'=>array(
				array('firstname'=>$regex),
				array('lastname'=>$regex),
				array('email'=>$regex),
				array('username'=>$regex)
			));
		}
		elseif (count($fields)) {
			foreach ($fields as $key=>$value) {
				if (is_string($value)) {
					$search[$key] = new MongoRegex("/$value/i");
				}
				elseif (is_array($value)) {
					$search[$key] = $value;
				}
			}
		}

		$this->runSearch($search,$order);
	}

	private function runSearch($search=null,$order=null)
	{
		if (count($search)) {
			$this->cursor = $this->mongo->people->find($search);
		}
		else {
			$this->cursor = $this->mongo->people->find();
		}
		if ($order) {
			$this->cursor->sort($order);
		}
	}

	/**
	 * Loads a single Person object for the row returned from ZendDbResultIterator
	 *
	 * @param array $key
	 */
	public function loadResult($data)
	{
		if ($data) {
			return new Person($data);
		}
		else {
			throw new Exception('resultIteratorSentEmptyData');
		}
	}
}
