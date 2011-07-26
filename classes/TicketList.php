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

	private $RETURN_TICKET_OBJECTS = true;
	private $DEFAULT_SORT = array('enteredDate'=>-1);

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
	public function find($fields=null,$order=null)
	{
		$this->RETURN_TICKET_OBJECTS = true;

		$search = self::prepareSearch($fields);
		if (count($search)) {
			$this->cursor = $this->mongo->tickets->find($search);
		}
		else {
			$this->cursor = $this->mongo->tickets->find();
		}
		$order = $order ? $order : $this->DEFAULT_SORT;
		if (count($this->cursor) < self::$MAX_SORTABLE_ROWS) {
			$this->cursor->sort($order);
		}
	}

	/**
	 * Populates the results with only the fields requested
	 *
	 * Hydrating whole Ticket objects for large searches can be slow
	 * This lets you get just the fields you need, saving memory
	 *
	 * @param array $fields
	 * @param array $order
	 * @param array $returnFields
	 */
	public function findRawData($fields=null,$order=null,$returnFields=null)
	{
		// Make sure there's always a TicketID in the results
		if (!is_array($returnFields)) {
			$returnFields = array();
		}
		if (!in_array('_id',$returnFields)) {
			$returnFields[] = '_id';
		}

		// Don't let them ask for just a small piece of person data
		// If they ask for any Person fields to be returned,
		// we should return the whole person record.
		// We'll expect the display to load that as a Person object
		// and call getter functions on it to display the information
		// See: /blocks/html/tickets/partials/searchResultRows.inc
		foreach ($returnFields as $i=>$field) {
			if (preg_match('/.*Person/',$field,$matches)) {
				$returnFields[$i] = $matches[0];
			}
		}

		$search = self::prepareSearch($fields);
		$this->cursor = $this->mongo->tickets->find($search,$returnFields);

		$order = $order ? $order : $this->DEFAULT_SORT;
		if (count($this->cursor) < self::$MAX_SORTABLE_ROWS) {
			$this->cursor->sort($order);
		}
		$this->RETURN_TICKET_OBJECTS = false;
	}

	private static function prepareSearch($fields)
	{
		$search = array();
		if (count($fields)) {
			foreach ($fields as $key=>$value) {
				if ($value) {
					if (is_array($value)) {
						// Convert any MongoIds
						if (false !== strpos($key,'_id')) {
							foreach ($value as $k=>$v) {
								$value[$k] = new MongoId($v);
							}
						}
						// We want to be able to pass raw Mongo queries for status
						// This should work, since we don't ever want to do queries
						// for a set of statuses.  We will only every be passing in
						// one status
						if ($key=='status') {
							$search[$key] = $value;
						}
						// Normally, we want to allow for passing in multiple possible values
						// We should do queries for tickets matching a field to a set of values
						else {
							$search[$key] = array('$in'=>$value);
						}
					}
					else {
						if (false !== strpos($key,'_id')) {
							$search[$key] = new MongoId($value);
						}
						// Make sure ticket numbers are converted to Integer
						// They won't be found if you pass a string
						elseif ($key=='number') {
							$search[$key] = (int)$value;
						}
						else {
							$search[$key] = $value;
						}
					}
				}
			}
		}
		return $search;
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
	 * Returns fields that can be displayed for search results
	 *
	 * We also provide the fully spelled-out mongo key for
	 * both searching and sorting
	 *
	 * The key of the returned array is the name used on search form inputs
	 * displayName is what should be displayed
	 * searchOn is the key to use for filtering on that field
	 * sortOn is the key to use for sorting by that field
	 *
	 * @return array(
	 *	fieldname=>array(
	 *			displayName=>human_readable_label,
	 *			searchOn=>mongoKey,
	 *			sortOn=>mongoKey
	 *		)
	 * )
	 */
	public static function getDisplayableFields()
	{
		// All possible columns to display
		return array(
			'id'=>array(
				'displayName'=>'Case ID','searchOn'=>'_id','sortOn'=>'_id'
			),
			'number'=>array(
				'displayName'=>'Case #','searchOn'=>'number','sortOn'=>'number'
			),
			'enteredDate'=>array(
				'displayName'=>'Case Date',
				'searchOn'=>'enteredDate',
				'sortOn'=>'enteredDate'
			),
			'enteredByPerson'=>array(
				'displayName'=>'Case Entered By',
				'searchOn'=>'enteredByPerson._id',
				'sortOn'=>'enteredByPerson.lastname'
			),
			'assignedPerson'=>array(
				'displayName'=>'Assigned To',
				'searchOn'=>'assignedPerson._id',
				'sortOn'=>'assignedPerson.lastname'
			),
			'referredPerson'=>array(
				'displayName'=>'Referred To',
				'searchOn'=>'referredPerson._id',
				'sortOn'=>'referredPerson.lastname'
			),
			'category'=>array(
				'displayName'=>'Category',
				'searchOn'=>'category._id',
				'sortOn'=>'category.name'
			),
			'department'=>array(
				'displayName'=>'Department',
				'searchOn'=>'assignedPerson.department._id',
				'sortOn'=>'assignedPerson.department.name'
			),
			'status'=>array('displayName'=>'Status','searchOn'=>'status','sortOn'=>'status'),
			'resolution'=>array('displayName'=>'Resolution','searchOn'=>'resolution','sortOn'=>'resolution'),
			'location'=>array('displayName'=>'Location','searchOn'=>'location','sortOn'=>'location'),
			'latitude'=>array('displayName'=>'Latitude','searchOn'=>'latitude','sortOn'=>'latitude'),
			'longitude'=>array('displayName'=>'Longitude','searchOn'=>'longitude','sortOn'=>'longitude'),
			'city'=>array('displayName'=>'City','searchOn'=>'city','sortOn'=>'city'),
			'state'=>array('displayName'=>'State','searchOn'=>'state','sortOn'=>'state'),
			'zip'=>array('displayName'=>'Zip','searchOn'=>'zip','sortOn'=>'zip'),
			'notes'=>array('displayName'=>'Notes','searchOn'=>'issues.notes','sortOn'=>'issues.notes')
		);
	}

	/**
	 * Returns the set of fields we want to display in search results by default
	 *
	 * @return array
	 */
	public static function getDefaultFieldsToDisplay()
	{
		return array('enteredDate'=>'on','location'=>'on','notes'=>'on');
	}
}
