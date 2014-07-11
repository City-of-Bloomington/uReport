<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class LabelTable extends TableGateway
{
    public function __construct() { parent::__construct('labels', __namespace__.'\Label'); }

	/**
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param bool $paginated Whether to return a paginator or a raw resultSet
	 * @param int $limit
	 */
	public function find($fields=null, $order='labels.name', $paginated=false, $limit=null)
	{
		$select = new Select('labels');
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					switch ($key) {
						case 'issue_id':
							$select->join(['i'=>'issue_labels'], 'labels.id=i.label_id', [], Select::JOIN_LEFT);
							$select->where(['i.issue_id' => $value]);
							break;
						default:
							$this->select->where(["labels.$key" => $value]);
					}
				}
			}
		}
		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
