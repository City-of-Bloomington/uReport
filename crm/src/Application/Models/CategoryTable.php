<?php
/**
 * A collection class for Category objects
 *
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
namespace Application\Models;

use Application\PdoRepository;

class CategoryTable extends PdoRepository
{
    public const TABLENAME = 'categories';
    public const CLASSNAME = __namespace__.'\Category';

    public function find(array $fields=[], ?string $order='g.ordering, g.name, c.name', ?int $itemsPerPage=null, ?int $currentPage=null): array
    {
        $select =  'select c.* from categories c';
        $joins  = ['left join categoryGroups g on g.id=c.categoryGroup_id'];
        $where  = [];
        $params = [];

        if ($fields) {
            foreach ($fields as $k=>$v) {
                switch ($k) {
                    case 'active':
                        $where[] = "c.active=:active";
                        $params['active'] = $v ? 1 : 0;
                        break;

                    case 'postableBy':
                        // If they are staff
                        if ($v instanceof Person && in_array($v->getRole(), ['Administrator', 'Staff'])) {
                            // no filtering
                            break;
                        }

                        $where[] = "c.postingPermissionLevel='public'";
                        break;

                    case 'displayableTo':
                        // If they are staff
                        if ($v instanceof Person && in_array($v->getRole(), ['Administrator', 'Staff'])) {
                            // no filtering
                            break;
                        }

                        $where[] = "c.displayPermissionLevel='public'";
                        break;

                    case 'department_id':
                        $joins[] = 'left join department_categories d on c.id=d.category_id';
                        $where[] = 'd.department_id=:department_id';
                        $params['department_id'] = $v;
                        break;

                    default:
                        if ($v) {
                            $where[] = "c.$k=:$k";
                            $params[$k] = $v;
                        }
                }
            }
        }
        // Only get categories this user is allowed to see or post to
        if (   !isset   ($_SESSION['USER'])
            || !in_array($_SESSION['USER']->getRole(), ['Administrator', 'Staff'])) {
            $where[] = "(c.postingPermissionLevel='public' or c.displayPermissionLevel='public')";
        }

        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }
}
