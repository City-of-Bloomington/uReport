<?php
/**
 * A collection class for Category objects
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class CategoryList extends MongoResultIterator
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
	public function find($fields=null,$order=null,$limit=null)
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
			$this->cursor = $this->mongo->tickets->find($search);
		}
		else {
			$this->cursor = $this->mongo->tickets->find();
		}
		if ($order) {
			$this->cursor->sort($order);
		}
		if ($limit) {
			$this->cursor->limit($limit);
		}
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
	
	/**
	 * @return string
	 */
	public function __toString()
	{
		$categories = array();
		foreach ($this as $category) {
			$categories[] = "{$category->getName()}";
		}
		return implode(', ',$categories);
	}
}
