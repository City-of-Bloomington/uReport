<?php
/**
 * A collection class for Category objects
 *
 * @copyright 2011-2018 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
namespace Application\Models;

use Blossom\Classes\TableGateway;
use Zend\Db\Sql\Select;

class CategoryTable extends TableGateway
{
	public function __construct() { parent::__construct('categories', __namespace__.'\Category'); }

	/**
	 * Populates the collection, using strict matching of the requested fields
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param bool $paginated Whether to return a paginator or a raw resultSet
	 * @param int $limit
	 */
	public function find($fields=null, $order=['g.ordering', 'g.name', 'categories.name'], $paginated=false, $limit=null)
	{
		$select = new Select('categories');
		$select->join(['g'=>'categoryGroups'], 'categories.categoryGroup_id=g.id', [], $select::JOIN_LEFT);

		if ($fields) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
                    case 'active':
                        $select->where(['categories.active'=>$value ? 1 : 0]);
                        break;

					case 'postableBy':
						// If they're authenticated, but they are not staff
						if ($value instanceof Person) {
							if ($value->getRole()!='Staff' && $value->getRole()!='Administrator') {
								// Limit them to public and anonymous categories
								$select->where(['categories.postingPermissionLevel'=>['public','anonymous']]);
							}
						}
						// They are not logged in. Limit them to anonymous categories
						else {
							$select->where(['categories.postingPermissionLevel'=>'anonymous']);
						}
						break;

					case 'displayableTo':
						// If they're authenticated, but they are not staff
						if ($value instanceof Person) {
							if ($value->getRole()!='Staff' && $value->getRole()!='Administrator') {
								// Limit them to public and anonymous categories
								$select->where(['categories.displayPermissionLevel'=>['public','anonymous']]);
							}
						}
						// They are not logged in. Limit them to anonymous categories
						else {
							$select->where(['categories.displayPermissionLevel'=>'anonymous']);
						}
						break;
					case 'department_id':
						$select->join(['d'=>'department_categories'], 'categories.id=d.category_id', [], $select::JOIN_LEFT);
						$select->where(['d.department_id'=>$value]);
						break;

					default:
						if ($value) {
							$select->where(["categories.$key"=>$value]);
						}
				}
			}
		}
		// Only get categories this user is allowed to see or post to
		if (!isset($_SESSION['USER'])) {
			$select->where("(categories.postingPermissionLevel='anonymous' or categories.displayPermissionLevel='anonymous')");
		}
		elseif ($_SESSION['USER']->getRole()!='Staff' && $_SESSION['USER']->getRole()!='Administrator') {
			$select->where("(categories.postingPermissionLevel in ('public','anonymous') or categories.displayPermissionLevel in ('public','anonymous'))");
		}

		return parent::performSelect($select, $order, $paginated, $limit);
	}
}
