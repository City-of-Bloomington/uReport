<?php
/**
 * A base class that streamlines creation of ZF2 TableGateway
 *
 * @copyright 2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Blossom\Classes;

use Zend\Db\TableGateway\TableGateway as ZendTableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

abstract class TableGateway
{
	protected $resultSetPrototype;
	protected $tableGateway;

	/**
	 * @param string $table The name of the database table
	 * @param string $class The class model to use as a resultSetPrototype
	 *
	 * You must pass in the fully namespaced classname.  We do not assume
	 * any particular namespace for the models.
	 */
	public function __construct($table, $class)
	{
		$this->resultSetPrototype = new ResultSet();
		$this->resultSetPrototype->setArrayObjectPrototype(new $class());
		$this->tableGateway = new ZendTableGateway(
			$table,
			Database::getConnection(),
			null,
			$this->resultSetPrototype
		);
	}

	/**
	 * Simple, default implementation for find
	 *
	 * This will allow you to do queries for rows in the table,
	 * where you provide field=>values for the where clause.
	 * Only fields actually in the table can be included this way.
	 *
	 * You generally want to override this implementation with your own
	 * However, this basic implementation will allow you to get up and
	 * running quicker.
	 *
	 * @param array $fields Key value pairs to select on
	 * @param string $order The default ordering to use for select
	 * @param boolean $paginated If set to true, will return a paginator
	 * @param int $limit
	 */
	public function find($fields=null, $order=null, $paginated=false, $limit=null)
	{
		$select = new Select($this->tableGateway->getTable());
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				$select->where([$key=>$value]);
			}
		}
		return $this->performSelect($select, $order, $paginated, $limit);
	}

	/**
	 * @param Zend\Db\Sql\Select $select
	 * @return Zend\Db\ResultSet
	 */
	public function performSelect(Select $select, $order, $paginated=false, $limit=null)
	{
		if ($order) { $select->order($order); }
		if ($limit) { $select->limit($limit); }

		if ($paginated) {
			$adapter = new DbSelect($select, $this->tableGateway->getAdapter(), $this->resultSetPrototype);
			$paginator = new Paginator($adapter);
			return $paginator;
		}
		else {
			return $this->tableGateway->selectWith($select);
		}
	}

	/**
	 * Returns the generated sql
	 *
	 * @param Zend\Db\Sql\Select
	 */
	public function getSqlForSelect(Select $select)
	{
		return $select->getSqlString($this->tableGateway->getAdapter()->getPlatform());
	}

	/**
	 * @param Zend\Db\ResultSet
	 */
	public static function getSqlForResult(ResultSet $result)
	{
		return $result->getDataSource()->getResource()->queryString;
	}
}
