<?php
/**
 * A collection class for Category objects
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class CategoryList extends ZendDbResultIterator
{
	/**
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		parent::__construct();

		$this->select->from(array('c'=>'categories'), 'c.*');
		$this->select->joinLeft(array('g'=>'categoryGroups'),'c.categoryGroup_id=g.id', array());

		if (is_array($fields)) {
			$this->find($fields);
		}
	}

	/**
	 * Populates the collection, using strict matching of the requested fields
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function find($fields=null,$order=array('g.ordering','g.name','c.name'),$limit=null,$groupBy=null)
	{
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'postableBy':
						// If they're authenticated, but they are not staff
						if ($value instanceof Person) {
							if ($value->getRole()!='Staff' && $value->getRole()!='Administrator') {
								// Limit them to public and anonymous categories
								$this->select->where("c.postingPermissionLevel in ('public','anonymous')");
							}
						}
						// They are not logged in. Limit them to anonymous categories
						else {
							$this->select->where('c.postingPermissionLevel=?','anonymous');
						}
						break;

					case 'displayableTo':
						// If they're authenticated, but they are not staff
						if ($value instanceof Person) {
							if ($value->getRole()!='Staff' && $value->getRole()!='Administrator') {
								// Limit them to public and anonymous categories
								$this->select->where("c.displayPermissionLevel in ('public','anonymous')");
							}
						}
						// They are not logged in. Limit them to anonymous categories
						else {
							$this->select->where('c.displayPermissionLevel=?','anonymous');
						}
						break;
					case 'department_id':
						$this->select->joinLeft(array('d'=>'department_categories'),'c.id=d.category_id', array());
						$this->select->where('d.department_id=?', $value);
						break;

					default:
						if ($value) {
							$this->select->where("c.$key=?",array($value));
						}
				}
			}
		}
		// Only get categories this user is allowed to see or post to
		if (!isset($_SESSION['USER'])) {
			$this->select->where("c.postingPermissionLevel='anonymous' or c.displayPermissionLevel='anonymous'");
		}
		elseif ($_SESSION['USER']->getRole()!='Staff' && $_SESSION['USER']->getRole()!='Administrator') {
			$this->select->where("c.postingPermissionLevel in ('public','anonymous') or c.displayPermissionLevel in ('public','anonymous')");
		}

		$this->select->order($order);
		if ($limit) {
			$this->select->limit($limit);
		}
		if ($groupBy) {
			$this->select->group($groupBy);
		}
	}

	/**
	 * Hydrates all the Category objects from a database result set
	 *
	 * @param int $key The index of the result row to load
	 * @return Category
	 */
	public function loadResult($key)
	{
		return new Category($this->result[$key]);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		$categories = array();
		foreach ($this as $category) {
			$categories[] = "{$category->getName()}";
		}
		return implode(', ',$categories);
	}
}
