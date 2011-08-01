<?php
/**
 * A collection class for Category objects
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
	 */
	public function find($fields=null,$order=array('name'=>1))
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
			$this->cursor = $this->mongo->categories->find($search);
		}
		else {
			$this->cursor = $this->mongo->categories->find();
		}
		if ($order) {
			$this->cursor->sort($order);
		}
	}

	/**
	 * Hydrates all the Category objects from a database result set
	 *
	 * @param int $key The index of the result row to load
	 * @return Category
	 */
	public function loadResult($data)
	{
		return new Category($data);
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
