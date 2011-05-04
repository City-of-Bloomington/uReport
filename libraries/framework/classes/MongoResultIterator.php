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
	
	abstract public function find($fields=null,$order=null);
	abstract public function loadResult($key);

	/**
	 * Creates an empty collection
	 */
	public function __construct()
	{
		$this->mongo = Database::getConnection();
	}

	/**
	 * @return array
	 */
	public function getExplain()
	{
		return $this->cursor->explain();
	}
	
	/**
	 * @param int $itemsPerPage
	 * @param int $currentPage
	 * @return Zend_Paginator
	 */
	public function getPaginator($itemsPerPage,$currentPage=1)
	{
		$paginator = new Zend_Paginator(new MongoPaginatorAdapter($this->cursor,$this));
		$paginator->setItemCountPerPage($itemsPerPage);
		$paginator->setCurrentPageNumber($currentPage);
		return $paginator;
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
