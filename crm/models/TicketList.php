<?php
/**
 * A collection class for Ticket objects
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
class TicketList extends MongoResultIterator
{
	/**
	 * Mongo has  a 4Mb limit on the data that can be sorted
	 * However, there doesn't seem to be a query where we can ask a cursor
	 * for how much data is about to be returned.
	 * So, instead we're using rowcount as a guide to decide
	 * whether we can apply sorting or not.
	 */
	public static $MAX_SORTABLE_ROWS = 2000;
	private static $DEFAULT_SORT = array('enteredDate'=>-1);

	private $RETURN_TICKET_OBJECTS = true;

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
		if (is_array($fields)) {
			$this->find($fields);
		}
	}

	/**
	 * Populates the results with the full record data of each Ticket
	 *
	 * All the parameters are passed as a single query array
	 * This is usually the raw $_REQUEST
	 * $query['sort'] Declares what sorting to use
	 * All other keys in $query are treated as Ticket fields to search on
	 *
	 * The emptyParameter is only a placeholder so that we can remain
	 * compatible with extending MongoResultIterator
	 *
	 * @param array $query
	 * @param null $emptyParameter Unused
	 */
	public function find($query=null,$emptyParameter=null)
	{
		$this->RETURN_TICKET_OBJECTS = true;

		$search = self::prepareSearch($query);
		$this->cursor = count($search)
			? $this->mongo->tickets->find($search)
			: $this->mongo->tickets->find();

		if ($this->cursor->count() < self::$MAX_SORTABLE_ROWS) {
			$this->cursor->sort(self::prepareSort($query));
		}
	}

	/**
	 * Populates the results with only the fields requested
	 *
	 * Hydrating whole Ticket objects for large searches can be slow
	 * This lets you get just the fields you need, saving memory
	 *
	 * All the parameters are now passed as a single query array
	 * This is usually the raw $_REQUEST
	 * $query['sort'] Declares what sorting to use
	 * $query['fields'] Declares what fields to return
	 * All other keys in $query are treated as Ticket fields to search on
	 *
	 * @param array $query
	 */
	public function findRawData($query=null)
	{
		$search = self::prepareSearch($query);
		$this->cursor = $this->mongo->tickets->find($search,self::prepareReturnFields($query));

		if ($this->cursor->count() < self::$MAX_SORTABLE_ROWS) {
			$this->cursor->sort(self::prepareSort($query));
		}
		$this->RETURN_TICKET_OBJECTS = false;
	}

	/**
	 * Populates the results using a raw mongo query
	 */
	public function findByMongoQuery($query)
	{
		$this->RETURN_TICKET_OBJECTS = true;
		$this->cursor = $this->mongo->tickets->find($query);
	}

	/**
	 * Takes the request and creates a Mongo search array
	 *
	 * Each of the fields passed in can be an array or a string
	 *
	 * @param array $request Usually the raw $_REQUEST
	 */
	private static function prepareSearch($request)
	{
		$search = array();
		if (count($request)) {
			foreach (self::getSearchableFields() as $name=>$index) {
				if (isset($request[$name]) && $request[$name]) {
					// First, check for and convert any MongoIds
					if (false !== strpos($index,'_id')) {
						if (is_array($request[$name])) {
							foreach ($request[$name] as $k=>$v) {
								$request[$name][$k] = new MongoId($v);
							}
						}
						else {
							$request[$name] = new MongoId($request[$name]);
						}
					}

					// Now go through all the fields and create the Mongo search array
					switch ($name) {
						case 'status':
							// We'll not be passing in multiple statuses during search
							// However, we will be passing in raw Mongo queries for status
							// These are arrays also, just not to be confused with wanting
							// to search for multiple statuses.
							$search[$index] = $request[$name];
							break;

						case 'number':
							$search[$index] = (int)$request[$name];
							break;

						case 'start_date':
							$search[$index]['$gt'] = new MongoDate(strtotime($request[$name]));
							break;

						case 'end_date':
							$search[$index]['$lt'] = new MongoDate(strtotime($request[$name]));
							break;

						default:
							// For all the other fields,
							// we might be passing in an array of values
							// or just a single value to look for.
							$search[$index] = is_array($request[$name])
								? array('$in'=>$request[$name])
								: $request[$name];
					}
				}
			}
		}

		// Only get tickets for categories this user is allowed to see
		if (!isset($_SESSION['USER'])) {
			$search['category.displayPermissionLevel'] = 'anonymous';
		}
		elseif ($_SESSION['USER']->getRole()!='Staff' && $_SESSION['USER']->getRole()!='Administrator') {
			$search['category.displayPermissionLevel'] = array('$in'=>array('public','anonymous'));
		}

		return $search;
	}

	/**
	 * Takes the request and returns a valid Mongo sorting
	 *
	 * @param array $request Usually the raw $_REQUEST
	 * @return array
	 */
	public static function prepareSort($request)
	{
		$fields = self::getSortableFields();
		$sort = self::$DEFAULT_SORT;

		if (!empty($request['sort'])) {
			$keys = array_keys($request['sort']);
			$fieldToSortBy = $keys[0];
			$value = $request['sort'][$fieldToSortBy];

			if (array_key_exists($fieldToSortBy,$fields)) {
				$sort = array($fields[$fieldToSortBy]=>(int)$value);
			}
		}

		return $sort;
	}

	/**
	 * Reads what the request wants displayed and prepares the Mongo query
	 *
	 * The return fields are declared as the keys to an array:
	 * $request['fields'] = array(
	 *	'enteredByPerson'=>'On','enteredDate'=>'On'
	 *);
	 *
	 * All the possible fieldnames are declared in self::getDisplayableFields()
	 *
	 * @param array $request Usually the raw $_REQUEST
	 * @return array
	 */
	public static function prepareReturnFields($request)
	{
		// Make sure there's always a Ticket_id
		$returnFields = array('_id'=>1);

		foreach (self::getDisplayableFields() as $name=>$definition) {
			if (isset($request['fields'][$name])) {
				$returnFields[$definition['index']] = 1;
			}
		}

		return $returnFields;
	}

	/**
	 * Hydrates all the Ticket objects from a database result set
	 *
	 * @param array $data A single data record returned from Mongo
	 * @return Ticket
	 */
	public function loadResult($data)
	{
		return $this->RETURN_TICKET_OBJECTS ? new Ticket($data) : $data;
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
