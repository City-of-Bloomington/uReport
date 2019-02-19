<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Application\TableGateway;
use Zend\Db\Sql\Select;

class DepartmentTable extends TableGateway
{
    public function __construct() { parent::__construct('departments', __namespace__.'\Department'); }

    /**
     * @param array $fields
     * @param string|array $order Multi-column sort should be given as an array
     * @param bool $paginated Whether to return a paginator or a raw resultSet
     * @param int $limit
     */
    public function find($fields=null, $order='name', $paginated=false, $limit=null)
    {
        return parent::find($fields, $order, $paginated, $limit);
	}
}
