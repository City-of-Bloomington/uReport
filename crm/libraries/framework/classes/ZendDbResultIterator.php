<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 *
 */
abstract class ZendDbResultIterator implements ArrayAccess,SeekableIterator,Countable
{
	protected $zend_db;
	protected $select;
	protected $result = array();

	protected $paginator = null;
	protected $itemsPerPage = null;
	protected $currentPage = 1;

	private $valid = false;
	private $cacheEnabled = true;
	private $cache = array();
	private $key;


	abstract public function find($fields=null,$order='',$limit=null,$groupBy=null);
	abstract protected function loadResult($key);

	/**
	 * Creates an empty collection
	 *
	 * Setting itemsPerPage turns on pagination mode
	 * In pagination mode, this will only load the results for one page
	 */
	public function __construct($itemsPerPage=null,$currentPage=null)
	{
		$this->zend_db = Database::getConnection();
		$this->select = new Zend_Db_Select($this->zend_db);

		if ($itemsPerPage) {
			$this->itemsPerPage = (integer)$itemsPerPage;

			if ($currentPage && $currentPage > 1) {
				$this->currentPage = $currentPage;
			}
		}
	}

	/**
	 * Runs the query and stores the results
	 *
	 * In pagination mode, this will only load the results for one page
	 */
	protected function populateList()
	{
		$this->result = array();
		if (!$this->itemsPerPage) {
			$this->result = $this->zend_db->fetchAll($this->select);
		}
		else {
			// Only load the results for one page
			$this->paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($this->select));
			$this->paginator->setItemCountPerPage($this->itemsPerPage);
			$this->paginator->setCurrentPageNumber($this->currentPage);
			foreach ($this->paginator as $row) {
				$this->result[] = $row;
			}
		}
	}

	/**
	 * @return string
	 */
	public function getSQL()
	{
		return $this->select->__toString();
	}

	/**
	 * @return Zend_Paginator
	 */
	public function getPaginator()
	{
		return $this->paginator;
	}

	// Array Access section
	/**
	 * @param int $offset
	 * @return boolean
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset,$this->result);
	}
	/**
	 * Unimplemented stub requried for interface compliance
	 * @ignore
	 */
	public function offsetSet($offset,$value) { } // Read-only for now
	/**
	 * Unimplemented stub requried for interface compliance
	 * @ignore
	 */
	public function offsetUnset($offset) { } // Read-only for now
	/**
	 * @param int $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		if ($this->offsetExists($offset)) {
			return $this->loadResult($offset);
		}
		else {
			throw new OutOfBoundsException('Invalid seek position');
		}
	}



	// SPLIterator Section
	/**
	 * Reset the pionter to the first element
	 */
	public function rewind() {
		$this->key = 0;
	}
	/**
	 * Advance to the next element
	 */
	public function next() {
		$this->key++;
	}
	/**
	 * Return the index of the current element
	 * @return int
	 */
	public function key() {
		return $this->key;
	}
	/**
	 * @return boolean
	 */
	public function valid() {
		return array_key_exists($this->key,$this->result);
	}
	/**
	 * @return mixed
	 */
	public function current()
	{
		return $this->loadResult($this->key);
	}
	/**
	 * @param int $index
	 */
	public function seek($index)
	{
		if (isset($this->result[$index])) {
			$this->key = $index;
		}
		else {
			throw new OutOfBoundsException('Invalid seek position');
		}
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
		return count($this->result);
	}
}
