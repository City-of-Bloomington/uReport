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
                        // If they're authenticated, but they are not staff
                        if ($v instanceof Person) {
                            if ($v->getRole()!='Staff' && $v->getRole()!='Administrator') {
                                // Limit them to public and anonymous categories
                                $where[] = "c.postingPermissionLevel in ('public', 'anonymous')";
                            }
                        }
                        // They are not logged in. Limit them to anonymous categories
                        else {
                            $where[] = "c.postingPermissionLevel='anonymous'";
                        }
                        break;

                    case 'displayableTo':
                        // If they're authenticated, but they are not staff
                        if ($v instanceof Person) {
                            if ($v->getRole()!='Staff' && $v->getRole()!='Administrator') {
                                // Limit them to public and anonymous categories
                                $where[] = "c.displayPermissionLevel in ('public', 'anonymous')";
                            }
                        }
                        // They are not logged in. Limit them to anonymous categories
                        else {
                            $where[] = "c.displayPermissionLevel='anonymous'";
                        }
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
        if (!isset($_SESSION['USER'])) {
            $where[] = "(c.postingPermissionLevel='anonymous' or c.displayPermissionLevel='anonymous')";
        }
        elseif ($_SESSION['USER']->getRole()!='Staff' && $_SESSION['USER']->getRole()!='Administrator') {
            $where[] = "(c.postingPermissionLevel in ('public','anonymous') or c.displayPermissionLevel in ('public','anonymous'))";
        }

        $sql  = parent::buildSql($select, $joins, $where, null, $order);
        return  parent::performSelect($sql, $params, $itemsPerPage, $currentPage);
    }
}
