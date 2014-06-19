<?php
/**
 * A collection class for Ticket objects
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class TicketList extends ZendDbResultIterator
{
	/**
	 * The set of fields we want to display in search results by default
	 */
	public static $defaultFieldsToDisplay = array(
		'enteredDate'=>'on', 'location'=>'on', 'description'=>'on', 'category_id'=>'on'
	);

	/**
	 * @param array $fields
	 */
	public function __construct($fields=null)
	{
		parent::__construct();

		$this->select->from(array('t'=>'tickets'), 't.*');
		$this->select->joinLeft(array('c'=>'categories'), 't.category_id=c.id', array());

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
	public function find($fields=null,$order='t.enteredDate desc',$limit=null,$groupBy=null)
	{
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					switch ($key) {
						case 'reportedByPerson_id':
							$this->select->joinLeft(array('i'=>'issues'), 't.id=i.ticket_id', array());
							$this->select->where("i.$key=?", $value);
							break;
						case 'start_date':
							$d = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($value));
							$this->select->where("t.enteredDate>=?", array($d));
							break;
						case 'end_date':
							$d = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($value));
							$this->select->where("t.enteredDate<=?", array($d));
							break;
						case 'lastModified_before':
							$d = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($value));
							$this->select->where('t.lastModified<=?', array($d));
							break;
						case 'lastModified_after':
							$d = date(ActiveRecord::MYSQL_DATE_FORMAT, strtotime($value));
							$this->select->where('t.lastModified>=?', array($d));
							break;
						case 'bbox':
							$bbox = explode(',', $value);
							if (count($bbox) == 4) {
								$minLat  = (float)$bbox[0];
								$minLong = (float)$bbox[1];
								$maxLat  = (float)$bbox[2];
								$maxLong = (float)$bbox[3];
								$this->select->where('t.latitude is not null and t.longitude is not null');
								$this->select->where('t.latitude  > ?', $minLat);
								$this->select->where('t.longitude > ?', $minLong);
								$this->select->where('t.latitude  < ?', $maxLat);
								$this->select->where('t.longitude < ?', $maxLong);
							}

							break;
						default:
							$this->select->where("t.$key=?", $value);
					}
				}
			}
		}
		// Only get tickets for categories this user is allowed to see
		if (!isset($_SESSION['USER'])) {
			$this->select->where("c.displayPermissionLevel='anonymous'");
		}
		elseif ($_SESSION['USER']->getRole()!='Staff'
				&& $_SESSION['USER']->getRole()!='Administrator') {
			$this->select->where("c.displayPermissionLevel in ('public','anonymous')");
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
	 * Loads a single object for the row returned from ZendDbResultIterator
	 *
	 * @param array $key
	 */
	protected function loadResult($key)
	{
		return new Ticket($this->result[$key]);
	}

	/**
	 * Returns fields that can be displayed in ticketList and searchResults
	 *
	 * The key should be the fieldname in the database
	 * The value should be a nice, readable label for the field
	 *
	 * @return array
	 */
	public static function getDisplayableFields()
	{
		$fields = array(
			'id'                => 'Case #',
			'enteredDate'       => 'Case Date',
			'enteredByPerson_id'=> 'Entered By',
			'assignedPerson_id' => 'Assigned To',
			'referredPerson_id' => 'Referred To',
			'category_id'       => 'Category',
			'department_id'     => 'Department',
			'status'            => 'Status',
			'substatus_id'      => 'Substatus',
			'location'          => 'Location',
			'latitude'          => 'Latitude',
			'longitude'         => 'Longitude',
			'city'              => 'City',
			'state'             => 'State',
			'zip'               => 'Zip',
			'description'       => 'Description',
			'slaPercentage'     => 'SLA'
		);
		foreach (AddressService::$customFieldDescriptions as $key=>$value) {
			$fields[$key] = $value['description'];
		}
		return $fields;
	}

	/**
	 * Returns fields this class knows how to search for
	 *
	 * These should match the fieldnames in the database
	 *
	 * @return array
	 */
	public static function getSearchableFields()
	{
		$fields = array(
			'enteredDate', 'description',
			'enteredByPerson_id', 'assignedPerson_id', 'referredPerson_id', 'reportedByPerson_id',
			'category_id', 'department_id', 'client_id',
			'status', 'substatus_id',
			'location', 'latitude', 'longitude',
			'city', 'state', 'zip',
			'start_date',
			'end_date',
			'label_id', 'issueType_id'
		);
		foreach (AddressService::$customFieldDescriptions as $key=>$value) {
			$fields[$key] = $key;
		}
		return $fields;
	}

	/**
	 * Tells whether the given request has fields that are searchable
	 *
	 * @param array $request Usually the raw $_REQUEST
	 * @return bool
	 */
	public static function isValidSearch($request)
	{
		return count(array_intersect(
			array_keys(self::getSearchableFields()),
			array_keys($request)
		)) ? true : false;
	}

}
