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
		'enteredDate'=>'on','location'=>'on','description'=>'on'
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
	public function find($fields=null,$order='t.enteredDate',$limit=null,$groupBy=null)
	{
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					switch ($key) {
						case 'status':
							if ($value == 'notClosed') {
								$this->select->where("t.status!='closed'");
							}
							else {
								$this->select->where("t.status=?", $value);
							}
							break;
						case 'reportedByPerson_id':
							$this->select->joinLeft(array('i'=>'issues'), 't.id=i.ticket_id', array());
							$this->select->where("i.$key=?", $value);
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
		$this->populateList();
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
	 * The displayName will be a nice, readable label for the field
	 * The index points to the desired data in the Mongo record
	 *
	 * @return array
	 */
	public static function getDisplayableFields()
	{
		$fields = array(
			'id'=>array('displayName'=>'Ticket ID','index'=>'_id'),
			'number'=>array('displayName'=>'Case #','index'=>'number'),
			'enteredDate'=>array('displayName'=>'Case Date','index'=>'enteredDate'),
			'enteredByPerson'=>array('displayName'=>'Entered By','index'=>'enteredByPerson'),
			'assignedPerson'=>array('displayName'=>'Assigned To','index'=>'assignedPerson'),
			'referredPerson'=>array('displayName'=>'Referred To','index'=>'referredPerson'),
			'category'=>array('displayName'=>'Category','index'=>'category.name'),
			'department'=>array('displayName'=>'Department','index'=>'assignedPerson'),
			'status'=>array('displayName'=>'Status','index'=>'status'),
			'resolution'=>array('displayName'=>'Resolution','index'=>'resolution'),
			'location'=>array('displayName'=>'Location','index'=>'location'),
			'latitude'=>array('displayName'=>'Latitude','index'=>'coordinates.latitude'),
			'longitude'=>array('displayName'=>'Longitude','index'=>'coordinates.longitude'),
			'city'=>array('displayName'=>'City','index'=>'city'),
			'state'=>array('displayName'=>'State','index'=>'state'),
			'zip'=>array('displayName'=>'Zip','index'=>'zip'),
			'description'=>array('displayName'=>'Description','index'=>'issues.description')
		);
		foreach (AddressService::$customFieldDescriptions as $key=>$value) {
			$fields[$key] = array('displayName'=>$value['description'],'index'=>$key);
		}
		return $fields;
	}

	/**
	 * Returns fields this class knows how to search for
	 *
	 * @return array
	 */
	public static function getSearchableFields()
	{
		$fields = array(
			'number'=>'number',
			'enteredDate'=>'enteredDate',
			'enteredByPerson'=>'enteredByPerson._id',
			'assignedPerson'=>'assignedPerson._id',
			'referredPerson'=>'referredPerson._id',
			'reportedByPerson'=>'issues.reportedByPerson._id',
			'category'=>'category._id',
			'department'=>'assignedPerson.department._id',
			'status'=>'status',
			'resolution'=>'resolution',
			'location'=>'location',
			'latitude'=>'coordinates.latitude',
			'longitude'=>'coordinates.longitude',
			'city'=>'city',
			'state'=>'state',
			'zip'=>'zip',
			'description'=>'issues.description',
			'start_date'=>'enteredDate',
			'end_date'=>'enteredDate',
			'client_id'=>'client_id',
			'labels'=>'issues.labels',
			'type'=>'issues.type'
		);
		foreach (AddressService::$customFieldDescriptions as $key=>$value) {
			$fields[$key] = $key;
		}
		return $fields;
	}

	/**
	 * Returns fields this class can use for sorting ticket list results
	 *
	 * @return array
	 */
	public static function getSortableFields()
	{
		$fields = array(
			'number'=>'number',
			'enteredDate'=>'enteredDate',
			'enteredByPerson'=>'enteredByPerson.lastname',
			'assignedPerson'=>'assignedPerson.lastname',
			'referredPerson'=>'referredPerson.lastname',
			'category'=>'category.name',
			'department'=>'assignedPerson.department.name',
			'status'=>'status',
			'resolution'=>'resolution',
			'location'=>'coordinates.location',
			'latitude'=>'coordinates.latitude',
			'longitude'=>'longitude',
			'city'=>'city',
			'state'=>'state',
			'zip'=>'zip',
			'description'=>'issues.description'
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
