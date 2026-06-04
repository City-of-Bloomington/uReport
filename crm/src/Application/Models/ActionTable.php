<?php
/**
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
declare (strict_types=1);
namespace Application\Models;

use Application\PdoRepository;

class ActionTable extends PdoRepository
{
    public const TABLENAME = 'actions';
    public const CLASSNAME = __namespace__.'\Action';

    public function find(array $fields=[], ?string $order='name', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select = 'select a.* from actions a';
        $joins  = [];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'department_id':
                        $joins[] = ['left join department_actions d on a.id=d.action_id'];
                        $where[] = 'd.department_id=:department_id';
                        $params['department_id'] = $v;
                        break;

                    default:
                        $where[] = "a.$k=:$k";
                        $params[$k] = $v;
                }
            }
        }
        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }
}
