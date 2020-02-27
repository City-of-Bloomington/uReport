<?php
/**
 * A base class that streamlines creation of TableGateway
 *
 * @copyright 2014-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application;

use Laminas\Db\TableGateway\TableGateway as LaminasTableGateway;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Select;
use Laminas\Paginator\Adapter\DbSelect;
use Laminas\Paginator\Paginator;

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
		$this->tableGateway = new LaminasTableGateway(
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
		if ($fields) {
			foreach ($fields as $key=>$value) {
                if (isset($this->columns)) {
                    if (in_array($key, $this->columns)) {
                        $select->where([$key=>$value]);
                    }
                }
                else {
                    $select->where([$key=>$value]);
                }
			}
		}
		return $this->performSelect($select, $order, $paginated, $limit);
	}

	/**
	 * @param  Select    $select
	 * @return ResultSet
	 */
	public function performSelect(Select $select, $order, $paginated=false, $limit=null)
	{
		if ($order) { $select->order($order); }
		if ($limit) { $select->limit($limit); }

		if ($paginated) {
			$adapter   = new DbSelect($select, $this->tableGateway->getAdapter(), $this->resultSetPrototype);
			$paginator = new Paginator($adapter);
			return $paginator;
		}
		else {
			return $this->tableGateway->selectWith($select);
		}
	}

	/**
	 * @param  ResultSet
	 * @return array
	 */
	public static function hydrateResults(ResultSet $results): array
	{
        $output = [];
        foreach ($results as $object) {
            $output[] = $object;
        }
        return $output;
	}

	/**
	 * Returns the generated sql
	 */
	public function getSqlForSelect(Select $select): string
	{
		return $select->getSqlString($this->tableGateway->getAdapter()->getPlatform());
	}

	public static function getSqlForResult(ResultSet $result): string
	{
		return $result->getDataSource()->getResource()->queryString;
	}
}
