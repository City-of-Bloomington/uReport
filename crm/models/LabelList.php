<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class LabelList extends ZendDbResultIterator
{
	public function __construct($fields=null)
	{
		parent::__construct();
		$this->select->from(array('l'=>'labels'), 'l.*');
		if (is_array($fields)) { $this->find($fields); }
	}

	/**
	 * Populates the collection
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function find($fields=null,$order='l.name',$limit=null,$groupBy=null)
	{
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					switch ($key) {
						case 'issue_id':
							$this->select->joinLeft(array('i'=>'issue_labels'), 'l.id=i.label_id', array());
							$this->select->where('i.issue_id=?', $value);
							break;
						default:
							$this->select->where("l.$key=?", $value);
					}
				}
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
	 * Loads a single object for the row returned from ZendDbResultIterator
	 *
	 * @param array $key
	 */
	protected function loadResult($key)
	{
		return new Label($this->result[$key]);
	}
}