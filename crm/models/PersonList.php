<?php
/**
 * A collection class for Person objects
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class PersonList extends ZendDbResultIterator
{
	private static $defaultSort = array('p.lastname', 'p.firstname');
	private static $fields = array(
		'firstname','middlename','lastname',
		'email','organization',
		'address','city','state','zip',
		'department_id','username','authenticationMethod','role'
	);

	public function __construct($fields=null)
	{
		parent::__construct();

		$this->select->from(array('p'=>'people'), 'p.*');
		$this->select->joinLeft(array('phone'=>'phones'), 'p.id=phone.person_id', array());

		if (is_array($fields)) { $this->find($fields); }
	}

	/**
	 * Populates the collection, using strict matching of the requested fields
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function find($fields=null, $order=null, $limit=null, $groupBy=null)
	{
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					switch ($key) {
						case 'user_account':
							$value
								? $this->select->where('username is not null')
								: $this->select->where('username is null');
							break;

						case 'phoneNumber':
							$this->select->where('phone.number=?', $value);
							break;

						case 'phoneDeviceId':
							$this->select->where('phone.deviceId=?', $value);
							break;

						default:
							if (in_array($key, self::$fields)) {
								$this->select->where("p.$key=?", $value);
							}
					}
				}
			}
		}

		$this->runSearch($order, $limit, $groupBy);
	}

	/**
	 * Populates the collection, using regular expressions for matching
	 *
	 * @param array $fields
	 * @param string|array $order Multi-column sort should be given as an array
	 * @param int $limit
	 * @param string|array $groupBy Multi-column group by should be given as an array
	 */
	public function search($fields=null, $order=null, $limit=null, $groupBy=null)
	{
		$search = array();
		if (isset($fields['query'])) {
			$value = trim($fields['query']).'%';
			$this->select->where ('p.firstname like ?', $value)
						->orWhere('p.lastname like ?',  $value)
						->orWhere('p.email like ?',     $value)
						->orWhere('p.username like ?',  $value);
		}
		elseif (count($fields)) {
			foreach ($fields as $key=>$value) {
				switch ($key) {
					case 'phoneNumber':
						$this->select->where('phone.number like ?', $value);
						break;
					case 'phoneDeviceId':
						$this->select->where('phone.deviceId like ?', $value);
						break;
					case 'department_id':
						$this->select->where('p.department_id=?', $value);
						break;
					default:
						$this->select->where("p.$key like ?", "$value%");
				}
			}
		}

		$this->runSearch($order, $limit, $groupBy);
	}

	private function runSearch($order=null, $limit=null, $groupBy=null)
	{
		if (!$order) { $order = self::$defaultSort; }
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
		return new Person($this->result[$key]);
	}
}
