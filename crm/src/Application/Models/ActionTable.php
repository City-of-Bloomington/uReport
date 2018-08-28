<?php
/**
 * @copyright 2011-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class ActionTable extends TableGateway
{
	public function __construct() { parent::__construct('actions', __namespace__.'\Action'); }

	/**
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param bool $paginated Whether to return a paginator or a raw resultSet
	 * @param int $limit
	 */
	public function find($fields=null, $order='name', $paginated=false, $limit=null)
	{
		$select = new Select('actions');
		if ($fields) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'department_id':
						$select->join(['d'=>'department_actions'], 'actions.id=d.action_id', [], $select::JOIN_LEFT);
						$select->where(['d.department_id' => $value]);
						break;
					default:
						$select->where([$key=>$value]);
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
