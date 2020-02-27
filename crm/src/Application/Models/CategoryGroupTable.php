<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Application\TableGateway;
use Laminas\Db\Sql\Select;

class CategoryGroupTable extends TableGateway
{
	public function __construct() { parent::__construct('categoryGroups', __namespace__.'\CategoryGroup'); }

	public function find($fields=null, $order=['ordering', 'name'], $paginated=false, $limit=null)
	{
		return parent::find($fields, $order, $paginated, $limit);
	}
}
