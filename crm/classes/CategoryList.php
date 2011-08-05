<?php
/**
 * A collection class for Category objects
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class CategoryList extends MongoResultIterator
{
	/**
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		parent::__construct();
		if (is_array($fields)) {
			$this->find($fields);
		}
	}

	/**
	 * Populates the collection, using strict matching of the requested fields
	 *
	 * @param array $fields
	 * @param array $order
	 */
	public function find($fields=null,$order=array('name'=>1))
	{
		$search = array();
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'postableBy':
						// If they're authenticated, but they are not staff
						if ($value instanceof Person) {
							if (!$value->hasRole('Staff') && !$value->hasRole('Administrator')) {
								// Limit them to public and anonymous categories
								$search['$or'] = array(
									array('postingPermissionLevel'=>'public'),
									array('postingPermissionLevel'=>'anonymous')
								);
							}
						}
						// They are not logged in. Limit them to anonymous categories
						else {
							$search['postingPermissionLevel'] = 'anonymous';
						}
						break;

					case 'displayableTo':
						// If they're authenticated, but they are not staff
						if ($value instanceof Person) {
							if (!$value->hasRole('Staff') && !$value->hasRole('Administrator')) {
								// Limit them to public and anonymous categories
								$search['$or'] = array(
									array('displayPermissionLevel'=>'public'),
									array('displayPermissionLevel'=>'anonymous')
								);
							}
						}
						// They are not logged in. Limit them to anonymous categories
						else {
							$search['displayPermissionLevel'] = 'anonymous';
						}
						break;

					default:
						if ($value) {
							$search[$key] = $value;
						}
				}
			}
		}
		// Only get categories this user is allowed to see or post to
		if (!isset($_SESSION['USER'])) {
			$search['$or'] = array(
				array('postingPermissionLevel'=>'anonymous'),
				array('displayPermissionLevel'=>'anonymous')
			);
		}
		elseif (!$_SESSION['USER']->hasRole('Staff') && !$_SESSION['USER']->hasRole('Administrator')) {
			$search['$or'] = array(
				array('postingPermissionLevel'=>array('$in'=>array('public','anonymous'))),
				array('displayPermissionLevel'=>array('$in'=>array('public','anonymous')))
			);
		}

		if (count($search)) {
			$this->cursor = $this->mongo->categories->find($search);
		}
		else {
			$this->cursor = $this->mongo->categories->find();
		}
		if ($order) {
			$this->cursor->sort($order);
		}
	}

	/**
	 * Hydrates all the Category objects from a database result set
	 *
	 * @param int $key The index of the result row to load
	 * @return Category
	 */
	public function loadResult($data)
	{
		return new Category($data);
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
