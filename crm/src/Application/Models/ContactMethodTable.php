<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Application\TableGateway;
use Laminas\Db\Sql\Select;

class ContactMethodTable extends TableGateway
{
	public function __construct() { parent::__construct('contactMethods', __namespace__.'\ContactMethod'); }

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
