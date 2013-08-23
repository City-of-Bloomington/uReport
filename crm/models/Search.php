<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once SOLR_PHP_CLIENT.'/Apache/Solr/Service.php';
class Search
{
	public $solrClient;
	public static $defaultSort = array('field'=>'enteredDate', 'order'=>'desc');

	const ITEMS_PER_PAGE = 10;
	const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

	/**
	 * The full list of fields that can be searched on
	 *
	 * Security Notice:
	 * Make sure to keep this initial list limited to "safe" fields.
	 * That is: fields that are okay to display to any anonymous person.
	 * Use __construct() to add fields that should be kept hidden
	 * unless a person is authorized to see them.
	 */
	public static $searchableFields = array(
		'id'              => 'Case #',
		'department_id'   => 'Department',
		'category_id'     => 'Category',
		'client_id'       => 'Client',
		'status'          => 'Status',
		'substatus_id'    => 'Substatus',
		'addressId'       => 'Adress ID',
		'location'        => 'Location',
		'city'            => 'City',
		'state'           => 'State',
		'zip'             => 'Zip',
		'issueType_id'    => 'Issue Type',
		'label_id'        => 'Label',
		'contactMethod_id'=> 'Received Via',
		'enteredDate'     => 'Case Date',
		'bbox'            => 'Bounding Box'  // Added by Quan on Aug 5, 2013
	);

	/**
	 * These are the base facets
	 *
	 * http://wiki.apache.org/solr/SolrFacetingOverview
	 *
	 * Security Notice:
	 * Make sure to keep this initial list limited to "safe" fields.
	 * That is: fields that are okay to display to any anonymous person.
	 * Use __construct() to add fields that should be kept hidden
	 * unless a person is authorized to see them.
	 */
	public static $facetFields = array(
		'ticket'=>array(
			'category_id',
			'department_id',
			'status',
			'client_id',
			'label_id',
			'issueType_id',
			'contactMethod_id'
		)
	);

	/**
	 * These are the fields that you can sort on
	 *
	 * Many of the fields we search on are ID fields.
	 * Rather than sorting on the ID fields, we really want to
	 * be able to sort on the string values they represent.
	 * Make sure the fields for the string values are indexed
	 */
	public static $sortableFields = array(
		'ticket'=>array(
			'enteredDate',
			'status',
			'location','city','state','zip',
			'department', 'category'
		)
	);

	/**
	 * Connects to Solr and adds additional facet fields
	 */
	public function __construct()
	{
		$this->solrClient = new Apache_Solr_Service(
			SOLR_SERVER_HOSTNAME,
			SOLR_SERVER_PORT,
			SOLR_SERVER_PATH
		);

		// Add facets for the AddressService custom fields
		foreach (AddressService::$customFieldDescriptions as $key=>$desc) {
			self::$searchableFields[$key] = $desc['description'];
			self::$facetFields['ticket'][] = $key;
			self::$sortableFields['ticket'][] = $key;
		}
		// Add facets that are only to be used if the current user is authorized
		if (userIsAllowed('people', 'view')) {
			self::$searchableFields['enteredByPerson_id']  = 'Entered By';
			self::$searchableFields['assignedPerson_id']   = 'Assigned To';
			self::$searchableFields['referredPerson_id']   = 'Referred To';
			self::$searchableFields['reportedByPerson_id'] = 'Reported By';

			self::$facetFields['ticket'][] = 'assignedPerson_id';

			self::$sortableFields['ticket'][] = 'enteredByPerson';
			self::$sortableFields['ticket'][] = 'assignedPerson';
			self::$sortableFields['ticket'][] = 'referredPerson';
			self::$sortableFields['ticket'][] = 'reportedByPerson';
		}
	}

	/**
	 * @param array $_GET
	 * @param string $recordType
	 * @return SolrObject
	 */
	public function query($get, $recordType=null)
	{
		$query = !empty($get['query'])
			? "{!df=description}$get[query]"
			: '*:*';
		
		$additionalParameters = array();
		$fq = array();
		
		if ($recordType) { $fq[] = "recordType:$recordType"; }

		// Pagination
		$rows = self::ITEMS_PER_PAGE;
		$start = 0;
		if (!empty($get['page'])) {
			$page = (int)$get['page'];
			if ($page < 1) { $page = 1; }

			// Solr rows start at 0, but pages start at 1
			$start = ($page-1) * self::ITEMS_PER_PAGE;
		}
		
		$sort = self::$defaultSort;
		if (isset($get['sort'])) {
			$keys = array_keys($_GET['sort']);
			$sort['field'] = $keys[0];
			$sort['order'] = ($_GET['sort'][$keys[0]] == 'asc')
				? 'asc'
				: 'desc';
		}
		
		$additionalParameters['sort'] = trim("$sort[field] $sort[order]");

		// Facets
		$additionalParameters['facet'] = 'true';
		$additionalParameters['facet.field'] = self::$facetFields['ticket'];

		// Search Parameters
		foreach (self::$searchableFields as $field=>$displayName) {
			if (!empty($get[$field])) {
				if (false !== strpos($field, 'Date')) {
					if (!empty($get[$field]['start']) || !empty($get[$field]['end'])) {
						$start = !empty($get[$field]['start'])
							? date(self::DATE_FORMAT, strtotime($get[$field]['start']))
							: '*';
						$end = !empty($get[$field]['end'])
							? date(self::DATE_FORMAT, strtotime($get[$field]['end']))
							: '*';
						$fq[] = "$field:[$start TO $end]";
					}
				}
				// Added else if statement by Quan on Aug 5, 2013
				// coordinates is a not a numeric value but does not need to be quoted.
				else if (false !== strpos($field, 'bbox')) {
					$key = 'coordinates';
					list($minLat, $minLng, $maxLat, $maxLng) = explode(',', $get[$field]);
					$value = "[$minLat,$minLng TO $maxLat,$maxLng]";
					$fq[] = "$key:$value";
				}
				else {
					$value = is_numeric($get[$field])
						? $get[$field]
						: "\"$get[$field]\"";
					$fq[] = "$field:$value";
				}
			}
		}

		// User permissions
		if (!isset($_SESSION['USER'])
			|| !in_array($_SESSION['USER']->getRole(), array('Administrator', 'Staff'))) {
			$permissions = 'anonymous';
			if (isset($_SESSION['USER']) && $_SESSION['USER']->getRole()=='Public') {
				$permissions.= ' OR public';
			}
			$fq[] = "displayPermissionLevel:$permissions";
		}

		if (count($fq)) { $additionalParameters['fq'] = $fq; }

		$solrResponse = $this->solrClient->search($query, $start, $rows, $additionalParameters);
		return $solrResponse;
	}

	/**
	 * @param Apache_Solr_Response $object
	 * @return array An array of CRM models based on the search results
	 */
	public static function hydrateDocs(Apache_Solr_Response $o)
	{
		$models = array();
		if (isset($o->response->docs) && $o->response->docs) {
			foreach ($o->response->docs as $doc) {
				switch ($doc->recordType) {
					case 'ticket':
						$models[] = new Ticket($doc->id);
						break;
				}
			}
		}
		else {
			header('HTTP/1.1 404 Not Found', true, 404);
		}
		return $models;
	}

	/**
	 * Indexes a single record in Solr
	 *
	 * @param mixed $record
	 */
	public function add($record)
	{
		$document = $this->createDocument($record);
		$this->solrClient->addDocument($document);
	}

	/**
	 * Removes a single record from Solr
	 *
	 * @param mixed $record
	 */
	public function delete($record)
	{
		if ($record instanceof Ticket) {
			$this->solrClient->deleteById('t_'.$record->getId());
		}
	}

	/**
	 * Indexes a whole collection of records in Solr
	 *
	 * @param mixed $list
	 */
	public function index($list)
	{
		foreach ($list as $record) {
			$this->add($record);
		}
	}

	/**
	 * Prepares a Solr Document with the correct fields for the record type
	 *
	 * @param mixed $record
	 * @return Apache_Solr_Document
	 */
	private function createDocument($record)
	{
		// These are the fields from the tickets table that we're indexing
		//
		// Note: enteredDate, latitude, longitude are indexed as well, even
		// though they are not in this list.
		// They are just handled slightly differently from the generic fields listed
		$ticketFields = array(
			'id', 'category_id', 'client_id',
			'enteredByPerson_id', 'assignedPerson_id', 'referredPerson_id',
			'addressId', 'location', 'city', 'state', 'zip',
			'status', 'substatus_id'
			// enteredDate, latitude, longitude
		);
		// These are the fields from the issues table that we're indexing
		$issueFields = array(
			'contactMethod_id', 'issueType_id', 'reportedByPerson_id'
		);

		if ($record instanceof Ticket) {
			$document = new Apache_Solr_Document();
			$document->addField('recordKey', "t_{$record->getId()}");
			$document->addField('recordType', 'ticket');

			$document->addField('enteredDate', $record->getEnteredDate(Search::DATE_FORMAT), DateTimeZone::UTC);
			if ($record->getLatLong()) {
				$document->addField('coordinates', $record->getLatLong());
			}
			if ($record->getCategory()) {
				$document->addField('displayPermissionLevel', $record->getCategory()->getDisplayPermissionLevel());
			}

			// Ticket information indexing
			foreach ($ticketFields as $f) {
				$get = 'get'.ucfirst($f);
				if ($record->$get()) {
					$document->addField($f, $record->$get());
					if (substr($f, -3) == '_id') {
						$document->addField(substr($f, 0, -3), $this->sortableString($record, $f));
					}
				}
			}
			$person = $record->getAssignedPerson();
			if ($person && $person->getDepartment_id()) {
				$document->addField('department_id', $person->getDepartment_id());
				$document->addField('department', $this->sortableString($person, 'department_id'));
			}

			// Issue information indexing
			$description = '';
			foreach ($record->getIssues() as $issue) {
				$description.= $issue->getDescription();
				foreach ($issueFields as $f) {
					$get = 'get'.ucfirst($f);
					if ($issue->$get()) {
						$document->addField($f, $issue->$get());
						$document->addField(substr($f, 0, -3), $this->sortableString($issue, $f));
					}
				}
				foreach ($issue->getLabels() as $id=>$label) {
					$document->addField('label_id', $id);
					$document->addField('label', $label->getName());
				}
			}
			if ($description) {
				$document->addField('description', $description);
			}

			// Index extra fields provided by the AddressService
			$additionalFields = $record->getAdditionalFields();
			if ($additionalFields) {
				foreach ($additionalFields as $key=>$value) {
					if ($value) {
						$document->addField($key, $value);
					}
				}
			}
			
			// GeoHash indexing, get geohash prefix from length 1 to 8
			if ($record->getLatitude() && $record->getLongitude()) {
				$latitude = $record->getLatitude();
				$longitude = $record->getLongitude();
				$document->addField('latitude', $latitude);
				$document->addField('longitude', $longitude);
				$geohash = new GeoHash();
				$geohash->setLatitude($latitude);
				$geohash->setLongitude($longitude);
				$hashcode = $geohash->getHash();
				for($i = 1; $i <= 8; $i++) {
					$prefix = substr($hashcode, 0, $i);
					$document->addField('geohash_lv'.$i, $prefix);
				}
			}

			return $document;
		}
	}

	/**
	 * Returns a string for the provided *_id field
	 *
	 * For sorting, we need to index the string values of fields,
	 * even though we're searching on the *_id value
	 *
	 * @param Ticket $record
	 * @param string $field
	 * @return string
	 */
	private function sortableString($record, $field)
	{
		$n = substr($field, 0, -3);
		$get = 'get'.ucfirst($n);
		$o = $record->$get();
		if (false !== strpos($field, 'Person')) {
			return "{$o->getLastname()} {$o->getFirstname()}";
		}
		else {
			return $o->getName();
		}
	}

	/**
	 * Returns the display name of a CRM object corresponding to a search field
	 *
	 * For each of the self::$searchableFields we need a way to look up the CRM
	 * object corresponding to the value in the search index.
	 * Example: self::getDisplayName('ticket', 'department_id', 32);
	 *
	 * @param string $recordType
	 * @param string $fieldname
	 * @param string $value
	 */
	public static function getDisplayName($recordType, $fieldname, $value)
	{
		if (isset(self::$searchableFields[$fieldname])) {
			if (false !== strpos($fieldname, 'Date')) {
				// Reformat Solr date ranges
				// enteredDate:[2011-06-15T00:00:00Z TO 2011-06-30T00:00:00Z]
				preg_match('/\[(.+)\sTO\s(.+)\]/', $value, $matches);
				$start = substr($matches[1], 0, 10);
				$end   = substr($matches[2], 0, 10);
				return "$start - $end";
			}
			elseif (false !== strpos($fieldname, 'Person_id')) {
				$o = new Person($value);
				return $o->getFullname();
			}
			elseif (false !== strpos($fieldname, '_id')) {
				$class = ucfirst(substr($fieldname, 0, -3));
				$o = new $class($value);
				return $o->getName();
			}
			else {
				return $value;
			}
		}
	}
}
