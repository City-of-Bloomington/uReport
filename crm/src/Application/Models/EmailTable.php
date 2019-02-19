<?php
/**
 * @copyright 2013-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Application\TableGateway;
use Zend\Db\Sql\Select;

class EmailTable extends TableGateway
{
	public function __construct() { parent::__construct('peopleEmails', __namespace__.'\Email'); }

	/**
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param bool $paginated Whether to return a paginator or a raw resultSet
	 * @param int $limit
	 */
	public function find($fields=null, $order='label', $paginated=false, $limit=null)
	{
		$select = new Select('peopleEmails');
		if ($fields) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					switch ($key) {
						case 'usedForNotifications':
							$select->where([$key => $value ? 1 : 0]);
							break;

						default:
							$select->where([$key=>$value]);
					}
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
