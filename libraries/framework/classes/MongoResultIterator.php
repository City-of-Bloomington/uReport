<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 *
 */
abstract class MongoResultIterator implements Iterator,Countable
{
	protected $mongo;
	protected $cursor;

	abstract public function find($fields=null,$order=null,$limit=null);
	abstract protected function loadResult($key);

	/**
	 * Creates an empty collection
	 *
	 * Setting itemsPerPage turns on pagination mode
	 * In pagination mode, this will only load the results for one page
	 */
	public function __construct()
	{
		$this->mongo = Database::getConnection();
	}

	// SPLIterator Section
	/**
	 * Reset the pionter to the first element
	 */
	public function rewind() {
		$this->cursor->rewind();
	}
	/**
	 * Advance to the next element
	 */
	public function next() {
		$this->cursor->next();
	}
	/**
	 * Return the index of the current element
	 * @return int
	 */
	public function key() {
		return $this->cursor->key();
	}
	/**
	 * @return boolean
	 */
	public function valid() {
		return $this->cursor->valid();
	}
	/**
	 * @return mixed
	 */
	public function current()
	{
		return $this->loadResult($this->cursor->current());
	}

	/**
	 * @return Iterator
	 */
	public function getIterator()
	{
		return $this;
	}

	// Countable Section
	/**
	 * @return int
	 */
	public function count()
	{
		return $this->cursor->count();
	}
}
