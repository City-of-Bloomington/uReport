<?php
/**
 * Takes an array and splits it up into pages (an array of arrays)
 *
 * @copyright 2008-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;

class Paginator implements ArrayAccess,SeekableIterator,Countable
{
	private $pageSize;
	private $pages = array();
	private $key = 0;

	/**
	 * @param array $list
	 * @param int $pageSize
	 */
	public function __construct($list,$pageSize)
	{
		$this->pageSize = $pageSize;
		$totalPageCount = count($list) / $this->pageSize;
		for ($i=0; $i<$totalPageCount; $i++) {
			$this->pages[] = $i * $this->pageSize;
		}
	}

	/**
	 * Returns the number of elements per page
	 * @return int
	 */
	public function getPageSize()
	{
		return $this->pageSize;
	}
	/**
	 * Returns the vary last index number for this paginator
	 * @return int
	 */
	public function getLastIndex()
	{
		return count($this->pages)-1;
	}

	// Array Access section
	/**
	 * @param int $offset
	 * @return boolean
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset,$this->pages);
	}
	/**
	 * Returns the array of elements for a given page
	 * @param int $offset
	 * @return array
	 */
	public function offsetGet($offset)
	{
		return $this->pages[$offset];
	}
	/**
	 * Unimplemented stub required for SPLIterator interface
	 * Once created, paginators are read-only
	 */
	public function offsetSet($offset,$value)
	{
		// Read-only for now
	}
	/**
	 * Unimplemented stub required for SPLIterator interface
	 * Once created, paginators are read-only
	 */
	public function offsetUnset($offset)
	{
		// Read only for now
	}


	// Iterator Interface stuff
	/**
	 * Reset the iterator to the start
	 */
	public function rewind()
	{
		$this->key = 0;
	}
	/**
	 * Move the pointer to the next element
	 */
	public function next()
	{
		$this->key++;
	}
	/**
	 * @return int
	 */
	public function key()
	{
		return $this->key;
	}
	/**
	 * @return boolean
	 */
	public function valid()
	{
		return array_key_exists($this->key,$this->pages);
	}
	/**
	 * Return the array of element for the current page
	 * @return array
	 */
	public function current()
	{
		return $this->pages[$this->key];
	}
	/**
	 * Go to a specific page
	 * @param int $index
	 */
	public function seek($index)
	{
		if (isset($this->pages[$index])) {
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

	// Countable interface section
	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->pages);
	}
}
