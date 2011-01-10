<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
*/
class RoleList extends ZendDbResultIterator
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
	 * Populates the collection
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function find($fields=null,$order='name',$limit=null,$groupBy=null)
	{
		$this->select->from('roles');

		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				$this->select->where("$key=?",$value);
			}
		}

		$this->select->order($order);
		if ($limit) {
			$this->select->limit($limit);
		}
		if ($groupBy) {
			$this->select->group($groupBy);
		}
		$this->populateList();
	}

	/**
	 * Load each Role object as we iterate through the results
	 *
	 * @return array An array of Role objects
	 */
	protected function loadResult($key)
	{
		return new Role($this->result[$key]);
	}
}
