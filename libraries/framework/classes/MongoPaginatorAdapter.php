<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class MongoPaginatorAdapter implements Zend_Paginator_Adapter_Interface
{
	protected $_cursor = null;
	protected $_iterator = null;
	
	public function __construct(MongoCursor $cursor, MongoResultIterator $iterator)
	{
		$this->_cursor = $cursor;
		$this->_iterator = $iterator;
	}
	
	public function count()
	{
		return $this->_cursor->count();
	}

    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
		$this->_cursor->skip($offset);
		$this->_cursor->limit($itemCountPerPage);
		
		$result = array();
		foreach ($this->_cursor as $item) {
			$result[] = $this->_iterator->loadResult($item);
		}
		return $result;
    }
}
